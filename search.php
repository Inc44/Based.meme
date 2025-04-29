<?php
require_once "db_connect.php";
session_start();
if (!isset($_SESSION["user_id"])) {
	header("Location: login.html");
	exit();
}
$pdo = getDbConnection();
try {
	$stmt = $pdo->prepare(
		"
SELECT
	tag_id,
	name,
	slug
FROM
	tags
ORDER BY
	usage_count DESC,
	name
LIMIT
	42
"
	);
	$stmt->execute();
	$tags = $stmt->fetchAll();
} catch (\PDOException $e) {
	throw $e;
	header("Location: status.php");
	exit();
} catch (\Exception $e) {
	throw $e;
}
$query = trim($_GET["query"] ?? "");
$selected_tags = array_filter(
	array_map("strval", (array) ($_GET["tags"] ?? []))
);
$type = $_GET["type"] ?? "memes";
$meme_results = [];
$user_results = [];
$tag_results = [];
$perform_search = $query !== "" || !empty($selected_tags) || $type !== "memes";
if ($perform_search) {
	try {
		$parameters = [];
		switch ($type) {
			case "memers":
				$sql = "
SELECT
	u.user_id,
	u.handle,
	u.display_name,
	u.avatar,
	SUBSTRING(u.bio, 1, 69) AS bio_snippet
FROM
	users AS u
WHERE
	u.is_banned = 0
				";
				if ($query !== "") {
					$sql .= "
AND (
	u.handle LIKE ?
	OR u.display_name LIKE ?
)
					";
					$parameters[] = "%$query%";
					$parameters[] = "%$query%";
				}
				$sql .= "
ORDER BY
	u.handle
LIMIT
	42
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute($parameters);
				$user_results = $stmt->fetchAll();
				$selected_tags = [];
				break;
			case "tags":
				$sql = "
SELECT
	t.tag_id,
	t.name,
	t.slug,
	t.usage_count
FROM
	tags AS t
				";
				if ($query !== "") {
					$sql .= "
WHERE
	(
		t.name LIKE ?
		OR t.slug LIKE ?
	)
					";
					$parameters[] = "%$query%";
					$parameters[] = "%$query%";
				}
				$sql .= "
ORDER BY
	t.usage_count DESC,
	t.name
LIMIT
	42
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute($parameters);
				$tag_results = $stmt->fetchAll();
				$selected_tags = [];
				break;
			case "memes":
			default:
				$type = "memes";
				$available = [
					"m.status = 'published'",
					"m.visibility = 'public'",
				];
				$sql = "
SELECT
	DISTINCT m.meme_id,
	m.title,
	m.slug,
	m.media_url,
	m.content,
	m.spicyness,
	m.view_count,
	m.like_count,
	m.comment_count,
	u.handle AS creator
FROM
	memes AS m
	JOIN users AS u ON m.user_id = u.user_id
				";
				if (!empty($selected_tags)) {
					$sql .= "
JOIN meme_tags AS mt ON m.meme_id = mt.meme_id
JOIN tags AS t ON mt.tag_id = t.tag_id
					";
					$placeholders = implode(
						",",
						array_fill(0, count($selected_tags), "?")
					);
					$available[] = "
t.slug IN ($placeholders)
					";
					$parameters = array_merge($parameters, $selected_tags);
				}
				if ($query !== "") {
					$available[] = "
(
	m.title LIKE ?
	OR m.content LIKE ?
)
					";
					$parameters[] = "%$query%";
					$parameters[] = "%$query%";
				}
				$sql .=
					"
WHERE
				" . implode(" AND ", $available);
				$sql .= "
ORDER BY
	m.published_at DESC
LIMIT
	42
				";
				$stmt = $pdo->prepare($sql);
				$stmt->execute($parameters);
				$meme_results = $stmt->fetchAll();
				if (!empty($meme_results)) {
					$meme_ids = array_column($meme_results, "meme_id");
					$placeholders = implode(
						",",
						array_fill(0, count($meme_ids), "?")
					);
					$sql = "
SELECT
	mt.meme_id,
	t.name,
	t.slug
FROM
	meme_tags AS mt
	JOIN tags AS t ON mt.tag_id = t.tag_id
WHERE
	mt.meme_id IN ($placeholders)
					";
					$stmt = $pdo->prepare($sql);
					$stmt->execute($meme_ids);
					$meme_tags = [];
					while ($row = $stmt->fetch()) {
						$meme_tags[$row["meme_id"]][] = $row["name"];
					}
					foreach ($meme_results as $key => $meme) {
						$meme_results[$key]["tags"] =
							$meme_tags[$meme["meme_id"]] ?? [];
					}
				}
				break;
		}
	} catch (\PDOException $e) {
		throw $e;
		header("Location: status.php");
		exit();
	} catch (\Exception $e) {
		throw $e;
	}
}
include "search.html";
?>
