<?php
require_once "db_connect.php";
session_start();
if (!isset($_SESSION["user_id"])) {
	if (isset($_COOKIE["remember_token"])) {
		$token = $_COOKIE["remember_token"];
		try {
			$pdo = getDbConnection();
			$stmt = $pdo->prepare("
SELECT
	u.user_id,
	u.handle
FROM
	user_sessions AS s
	JOIN users AS u ON s.user_id = u.user_id
WHERE
	s.session_id = ?
	AND s.expires_at > NOW()
			");
			$stmt->execute([$token]);
			$user = $stmt->fetch();
			if ($user) {
				$_SESSION["user_id"] = $user["user_id"];
				$_SESSION["handle"] = $user["handle"];
				$update = $pdo->prepare("
UPDATE
	user_sessions
SET
	last_activity = CURRENT_TIMESTAMP
WHERE
	session_id = ?
				");
				$update->execute([$token]);
			} else {
				setcookie("remember_token", "", time() - 3600, "/");
				header("Location: index.html");
				exit();
			}
		} catch (\PDOException $e) {
			throw $e;
			header("Location: status.php");
			exit();
		} catch (\Exception $e) {
			throw $e;
			header("Location: index.html");
			exit();
		}
	} else {
		header("Location: index.html");
		exit();
	}
}
try {
	$pdo = getDbConnection();
	$stmt = $pdo->prepare("
SELECT
	handle,
	display_name,
	avatar,
	joined_at
FROM
	users
WHERE
	user_id = ?
	");
	$stmt->execute([$_SESSION["user_id"]]);
	$user = $stmt->fetch();
	$stmt = $pdo->prepare("
SELECT
	m.meme_id,
	m.title,
	m.slug,
	m.media_url,
	m.content,
	m.spiciness,
	m.view_count,
	m.like_count,
	m.comment_count,
	u.handle AS creator
FROM
	memes AS m
	JOIN users u ON m.user_id = u.user_id
WHERE
	m.status = 'published'
	AND m.visibility = 'public'
ORDER BY
	RAND()
LIMIT
	3
	");
	$stmt->execute();
	$trendingMemes = $stmt->fetchAll();
	foreach ($trendingMemes as $key => $meme) {
		$tagStmt = $pdo->prepare("
SELECT
	t.name
FROM
	tags AS t
	JOIN meme_tags mt ON t.tag_id = mt.tag_id
WHERE
	mt.meme_id = ?
		");
		$tagStmt->execute([$meme["meme_id"]]);
		$tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
		$trendingMemes[$key]["tags"] = $tags;
	}
	$stmt = $pdo->prepare("
SELECT
	m.meme_id,
	m.title,
	m.slug,
	m.media_url,
	m.content,
	m.spiciness,
	m.view_count,
	m.like_count,
	m.comment_count,
	u.handle AS creator
FROM
	memes AS m
	JOIN users AS u ON m.user_id = u.user_id
WHERE
	m.status = 'published'
	AND m.visibility = 'public'
	AND m.meme_id NOT IN (?)
ORDER BY
	RAND()
LIMIT
	3
	");
	$trendingIds = array_column($trendingMemes, "meme_id");
	$idsToExclude = !empty($trendingIds) ? implode(",", $trendingIds) : "0";
	$stmt->execute([$idsToExclude]);
	$recommendedMemes = $stmt->fetchAll();
	foreach ($recommendedMemes as $key => $meme) {
		$tagStmt = $pdo->prepare("
SELECT
	t.name
FROM
	tags t
	JOIN meme_tags mt ON t.tag_id = mt.tag_id
WHERE
	mt.meme_id = ?
		");
		$tagStmt->execute([$meme["meme_id"]]);
		$tags = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
		$recommendedMemes[$key]["tags"] = $tags;
	}
} catch (\PDOException $e) {
	throw $e;
	header("Location: status.php");
	exit();
} catch (\Exception $e) {
	throw $e;
}
$joinDate = new DateTime($user["joined_at"] ?? date("Y-m-d H:i:s"));
$joinDateFormatted = $joinDate->format("F Y");
$displayName = !empty($user["display_name"])
	? $user["display_name"]
	: $user["handle"] ?? "Anon";
if (empty($trendingMemes)) {
	$trendingMemes = [
		[
			"meme_id" => "placeholder1",
			"title" => "When the code finally works, but you don't know why",
			"creator" => "CringeCoder",
			"like_count" => 4200,
			"comment_count" => 69,
			"media_url" => null,
			"tags" => [],
		],
		[
			"meme_id" => "placeholder2",
			"title" => "POV: You're explaining memes to your parents",
			"creator" => "BoomerTranslator",
			"like_count" => 2800,
			"comment_count" => 42,
			"media_url" => null,
			"tags" => [],
		],
		[
			"meme_id" => "placeholder3",
			"title" => "No one: My brain at 3 a.m.",
			"creator" => "InsomniaGuru",
			"like_count" => 6900,
			"comment_count" => 420,
			"media_url" => null,
			"tags" => [],
		],
	];
}
$categoryMappings = [
	"for-you" => ["name" => "For You", "icon" => "🔥"],
	"dank" => ["name" => "Dank", "icon" => "🎭"],
	"cursed" => ["name" => "Cursed", "icon" => "💀"],
	"shitpost" => ["name" => "Shitpost", "icon" => "🤡"],
	"big-brain" => ["name" => "Big Brain", "icon" => "🧠"],
	"surreal" => ["name" => "Surreal", "icon" => "👽"],
	"tech" => ["name" => "Tech", "icon" => "🖥️"],
	"gaming" => ["name" => "Gaming", "icon" => "🎮"],
	"social-media" => ["name" => "Social Media", "icon" => "📱"],
	"classic" => ["name" => "Classic", "icon" => "🗿"],
];
$activeSlug = "for-you";
if (isset($_GET["category"])) {
	$requestedSlug = $_GET["category"];
	if (array_key_exists($requestedSlug, $categoryMappings)) {
		$activeSlug = $requestedSlug;
	}
}
$categories = [];
foreach ($categoryMappings as $slug => $details) {
	$category = $details;
	$category["slug"] = $slug;
	$category["isActive"] = $slug === $activeSlug;
	$categories[] = $category;
}
include "index_logged_in.html";
?>
