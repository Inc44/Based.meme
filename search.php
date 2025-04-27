<?php
require_once "db_connect.php";
$pdo = getDbConnection();
$meme_id = $_GET["meme_id"] ?? "";
$tags = $_GET["tags"] ?? [];
$memes_by_id = [];
$memes_by_tags = [];
if ($meme_id !== "") {
	$stmt = $pdo->prepare("
SELECT
	*
FROM
	memes
WHERE
	meme_id = ?
	");
	$stmt->execute([$meme_id]);
	$memes_by_id = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if (!empty($tags)) {
	$placeholders = implode(",", array_fill(0, count($tags), "?"));
	$stmt = $pdo->prepare("
SELECT
	DISTINCT memes.*
FROM
	memes
	LEFT JOIN meme_tags ON memes.meme_id = meme_tags.meme_id
	LEFT JOIN tags ON meme_tags.tag_id = tags.tag_id
WHERE
	tags.slug IN ($placeholders)
	");
	$stmt->execute($tags);
	$memes_by_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
include "search.html";
?>
