<?php
require_once "db_connect.php";
session_start();
function yid(int $size = 11): string
{
	return implode(
		"",
		array_map(
			fn() => "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_"[
				random_int(0, 63)
			],
			range(1, $size)
		)
	);
}
$memeId = $_GET["id"] ?? null;
if (!$memeId) {
	header("Location: 404.php");
	exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (!isset($_SESSION["user_id"])) {
		$_SESSION["login_redirect"] = $_SERVER["REQUEST_URI"] ?? "index.php";
		header("Location: login.html");
		exit();
	}
	$userId = (int) $_SESSION["user_id"];
	$action = $_POST["action"] ?? "";
	try {
		$pdo = getDbConnection();
		switch ($action) {
			case "liked":
			case "cringe":
			case "based":
			case "steal":
				$interactionMap = [
					"liked" => ["like", "like_count"],
					"cringe" => ["dislike", "dislike_count"],
					"based" => ["upvote", "upvote_count"],
					"steal" => ["save", "saved_count"],
				];
				[$interactionType, $countColumn] = $interactionMap[$action];
				$stmt = $pdo->prepare('
SELECT
	1
FROM
	user_interactions
WHERE
	user_id = ?
	AND interaction_type = ?
	AND content_type = "meme"
	AND content_id = ?
				');
				$stmt->execute([$userId, $interactionType, $memeId]);
				if ($stmt->fetch()) {
					$pdo->prepare(
						'
DELETE FROM
	user_interactions
WHERE
	user_id = ?
	AND interaction_type = ?
	AND content_type = "meme"
	AND content_id = ?
					'
					)->execute([$userId, $interactionType, $memeId]);
					$pdo->prepare(
						"
UPDATE
	memes
SET
	$countColumn = $countColumn - 1
WHERE
	meme_id = ?
					"
					)->execute([$memeId]);
				} else {
					$pdo->prepare(
						'
INSERT INTO
	user_interactions (
		user_id,
		interaction_type,
		content_type,
		content_id
	)
VALUES
	(?, ?, "meme", ?)
					'
					)->execute([$userId, $interactionType, $memeId]);
					$pdo->prepare(
						"
UPDATE
	memes
SET
	$countColumn = $countColumn + 1
WHERE
	meme_id = ?
					"
					)->execute([$memeId]);
				}
				break;
			case "comment":
				$content = trim($_POST["comment"] ?? "");
				$parentId = $_POST["parent_id"] ?? null;
				if ($content !== "") {
					$commentId = yid();
					$pdo->prepare(
						"
INSERT INTO
	comments (comment_id, meme_id, user_id, content, parent_id)
VALUES
	(?, ?, ?, ?, ?)
					"
					)->execute([
						$commentId,
						$memeId,
						$userId,
						$content,
						$parentId ?: null,
					]);
					$pdo->prepare(
						"
UPDATE
	memes
SET
	comment_count = comment_count + 1
WHERE
	meme_id = ?
					"
					)->execute([$memeId]);
				}
				break;
			case "delete_comment":
				$commentId = $_POST["comment_id"] ?? "";
				if ($commentId) {
					$pdo->beginTransaction();
					$stmt = $pdo->prepare("
SELECT
	parent_id
FROM
	comments
WHERE
	comment_id = ?
	AND user_id = ?
					");
					$stmt->execute([$commentId, $userId]);
					$comment = $stmt->fetch();
					if ($comment) {
						$parentId = $comment["parent_id"];
						$pdo->prepare(
							"
UPDATE
	comments
SET
	parent_id = ?
WHERE
	parent_id = ?
						"
						)->execute([$parentId, $commentId]);
						$pdo->prepare(
							"
DELETE FROM
	comments
WHERE
	comment_id = ?
						"
						)->execute([$commentId]);
						$pdo->prepare(
							"
UPDATE
	memes
SET
	comment_count = comment_count - 1
WHERE
	meme_id = ?
						"
						)->execute([$memeId]);
					}
					$pdo->commit();
				}
				break;
			case "delete_meme":
				$stmt = $pdo->prepare("
SELECT
	1
FROM
	memes
WHERE
	meme_id = ?
	AND user_id = ?
				");
				$stmt->execute([$memeId, $userId]);
				if ($stmt->fetch()) {
					$pdo->prepare(
						"
DELETE FROM
	memes
WHERE
	meme_id = ?
					"
					)->execute([$memeId]);
					header("Location: index.php");
					exit();
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
	header("Location: " . $_SERVER["REQUEST_URI"]);
	exit();
}
try {
	$pdo = getDbConnection();
	$stmt = $pdo->prepare('
SELECT
	m.*,
	u.handle,
	u.display_name,
	u.avatar,
	u.joined_at
FROM
	memes AS m
	JOIN users AS u ON u.user_id = m.user_id
WHERE
	m.meme_id = ?
	AND m.status = "published"
	');
	$stmt->execute([$memeId]);
	$meme = $stmt->fetch();
	if (!$meme) {
		header("Location: 404.php");
		exit();
	}
	$joinDate = new DateTime($meme["joined_at"] ?? date("Y-m-d H:i:s"));
	$joinDateFormatted = $joinDate->format("F Y");
	$isOwner =
		isset($_SESSION["user_id"]) &&
		(int) $_SESSION["user_id"] === (int) $meme["user_id"];
	$stmt = $pdo->prepare("
SELECT
	t.tag_id,
	t.name
FROM
	meme_tags AS mt
	JOIN tags AS t ON t.tag_id = mt.tag_id
WHERE
	mt.meme_id = ?
	");
	$stmt->execute([$memeId]);
	$tags = $stmt->fetchAll();
	$flags = [
		"liked" => false,
		"cringe" => false,
		"based" => false,
		"steal" => false,
	];
	if (isset($_SESSION["user_id"])) {
		$stmt = $pdo->prepare('
SELECT
	interaction_type
FROM
	user_interactions
WHERE
	user_id = ?
	AND content_type = "meme"
	AND content_id = ?
		');
		$stmt->execute([$_SESSION["user_id"], $memeId]);
		$interactionMap = [
			"like" => "liked",
			"dislike" => "cringe",
			"upvote" => "based",
			"save" => "steal",
		];
		foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $interactionType) {
			if (isset($interactionMap[$interactionType])) {
				$flags[$interactionMap[$interactionType]] = true;
			}
		}
	}
	$stmt = $pdo->prepare("
SELECT
	c.*,
	u.handle,
	u.display_name,
	u.avatar
FROM
	comments AS c
	JOIN users AS u ON u.user_id = c.user_id
WHERE
	c.meme_id = ?
ORDER BY
	c.created_at ASC
	");
	$stmt->execute([$memeId]);
	$comments = $stmt->fetchAll();
	$commentsByID = [];
	$tree = [];
	foreach ($comments as $comment) {
		$comment["children"] = [];
		$commentsByID[$comment["comment_id"]] = $comment;
	}
	foreach ($commentsByID as &$comment) {
		if (
			$comment["parent_id"] &&
			isset($commentsByID[$comment["parent_id"]])
		) {
			$commentsByID[$comment["parent_id"]]["children"][] = &$comment;
		} else {
			$tree[] = &$comment;
		}
	}
	unset($comment);
	$tagIdsIn = implode(",", array_column($tags, "tag_id") ?: [0]);
	$titleKeywords =
		"%" . preg_replace("/[^ -_0-9A-Za-z]/", " ", $meme["title"]) . "%";
	$contentKeywords =
		"%" . preg_replace("/[^ -_0-9A-Za-z]/", " ", $meme["title"]) . "%";
	$stmt = $pdo->prepare("
SELECT
	DISTINCT m.meme_id,
	m.title,
	m.media_url,
	m.slug,
	m.spiciness,
	m.like_count,
	m.comment_count,
	u.handle AS creator,
	SUM(
		CASE
			WHEN mt.tag_id IN ($tagIdsIn) THEN 1
			ELSE 0
		END
	) AS tag_score
FROM
	memes AS m
	JOIN users AS u ON u.user_id = m.user_id
	LEFT JOIN meme_tags AS mt ON mt.meme_id = m.meme_id
WHERE
	m.meme_id <> ?
	AND m.status = 'published'
	AND m.visibility = 'public'
	AND (
		mt.tag_id IN ($tagIdsIn)
		OR m.title LIKE ?
		OR m.content LIKE ?
	)
GROUP BY
	m.meme_id
ORDER BY
	tag_score DESC,
	m.like_count DESC
LIMIT
	6
	");
	$stmt->execute([$memeId, $titleKeywords, $contentKeywords]);
	$uniqueMemes = [];
	foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $related) {
		$uniqueMemes[$related["meme_id"]] = $related;
	}
	if (count($uniqueMemes) < 3) {
		$excludeList = implode(
			",",
			array_map(
				[$pdo, "quote"],
				array_merge([$memeId], array_keys($uniqueMemes))
			)
		);
		$stmt = $pdo->query(
			"
SELECT
	m.meme_id,
	m.title,
	m.media_url,
	m.slug,
	m.spiciness,
	m.like_count,
	m.comment_count,
	u.handle AS creator
FROM
	memes AS m
	JOIN users AS u ON u.user_id = m.user_id
WHERE
	m.meme_id NOT IN ($excludeList)
	AND m.status = 'published'
	AND m.visibility = 'public'
ORDER BY
	RAND()
LIMIT
		" .
				(3 - count($uniqueMemes))
		);
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $similar) {
			$uniqueMemes[$similar["meme_id"]] = $similar;
		}
		while (count($uniqueMemes) < 3) {
			$id = "placeholder-" . count($uniqueMemes);
			$uniqueMemes[$id] = [
				"meme_id" => $id,
				"title" => "No similar memes found",
				"media_url" => "",
				"slug" => "",
				"creator" => "system",
				"like_count" => 0,
				"comment_count" => 0,
				"tags" => [],
			];
		}
	}
	$similarMemes = array_slice(array_values($uniqueMemes), 0, 3);
	foreach ($similarMemes as $key => $similar) {
		if (
			isset($similar["meme_id"]) &&
			!preg_match("/^(placeholder|duplicate)-/", $similar["meme_id"])
		) {
			$stmt = $pdo->prepare("
SELECT
	t.name,
	t.slug
FROM
	tags AS t
	JOIN meme_tags AS mt ON t.tag_id = mt.tag_id
WHERE
	mt.meme_id = ?
			");
			$stmt->execute([$similar["meme_id"]]);
			$similarMemes[$key]["tags"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else {
			$similarMemes[$key]["tags"] = [];
		}
	}
} catch (\PDOException $e) {
	throw $e;
	header("Location: status.php");
	exit();
} catch (\Exception $e) {
	throw $e;
}
include "meme.html";
?>
