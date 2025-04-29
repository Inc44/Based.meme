<?php
require_once "db_connect.php";
session_start();
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
	name
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
$results = [];
if ($query !== "" || !empty($selected_tags)) {
	try {
		$parameters = [];
		$available = ["m.status = 'published'", "m.visibility = 'public'"];
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
		$results = $stmt->fetchAll();
		if (!empty($results)) {
			$meme_ids = array_column($results, "meme_id");
			$placeholders = implode(",", array_fill(0, count($meme_ids), "?"));
			$sql = "
SELECT
	mt.meme_id,
	t.name
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
			foreach ($results as $key => $meme) {
				$results[$key]["tags"] = $meme_tags[$meme["meme_id"]] ?? [];
			}
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
