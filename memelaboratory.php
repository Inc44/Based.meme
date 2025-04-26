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
if (!isset($_SESSION["user_id"])) {
	header("Location: login.html");
	exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$userId = $_SESSION["user_id"];
	$title = trim($_POST["title"] ?? "");
	$content = trim($_POST["description"] ?? "");
	$postSpicy = $_POST["spiciness"] ?? 0;
	$cappedSpicy = min($postSpicy, 1);
	$spicy = (float) max($postSpicy, $cappedSpicy);
	$tagIds = array_map("intval", $_POST["tags"] ?? []);
	$file = $_FILES["image"] ?? null;
	$errors = [];
	if ($title === "") {
		$errors[] = "Title is required.";
	}
	if (!$file || $file["error"]) {
		$errors[] = "Image is required.";
	}
	if (!$errors) {
		$allow = [
			"image/avif",
			"image/bmp",
			"image/gif",
			"image/heic",
			"image/heif",
			"image/jpeg",
			"image/jxl",
			"image/png",
			"image/svg+xml",
			"image/tiff",
			"image/webp",
		];
		$mime = mime_content_type($file["tmp_name"]);
		if (!in_array($mime, $allow, true)) {
			$errors[] = "Unsupported type.";
		}
	}
	if ($errors) {
		$_SESSION["upload_errors"] = $errors;
		header("Location: memelaboratory.php");
		exit();
	}
	$memeId = yid();
	$uploadId = yid();
	$ext = pathinfo($file["name"], PATHINFO_EXTENSION);
	$path = "uploads/$uploadId.$ext";
	if (!is_dir("uploads")) {
		mkdir("uploads");
	}
	move_uploaded_file($file["tmp_name"], $path);
	$pdo = getDbConnection();
	$pdo->beginTransaction();
	try {
		$stmt = "
INSERT INTO
	memes (
		meme_id,
		slug,
		user_id,
		title,
		content,
		media_url,
		status,
		visibility,
		spicyness,
		published_at
	)
VALUES
	(?, ?, ?, ?, ?, ?, 'published', 'public', ?, NOW())
		";
		$slug =
			strtolower(preg_replace("/[^a-z0-9]+/", "-", $content)) .
			"-" .
			$memeId;
		$pdo->prepare($stmt)->execute([
			$memeId,
			$slug,
			$userId,
			$title,
			$content,
			$path,
			$spicy,
		]);
		$stmt = "
INSERT INTO
	uploads (
		upload_id,
		user_id,
		file_name,
		file_path,
		file_type,
		file_size,
		meme_id,
		width,
		height
	)
VALUES
	(?, ?, ?, ?, ?, ?, ?, NULL, NULL)
		";
		$pdo->prepare($stmt)->execute([
			$uploadId,
			$userId,
			$file["name"],
			$path,
			$mime,
			$file["size"],
			$memeId,
		]);
		if ($tagIds) {
			$stmt = $pdo->prepare("
INSERT INTO
	meme_tags (meme_id, tag_id)
VALUES
	(?, ?)
			");
			foreach ($tagIds as $tid) {
				$stmt->execute([$memeId, $tid]);
				$pdo->prepare(
					"
UPDATE
	tags
SET
	usage_count = usage_count + 1
WHERE
	tag_id = ?
					"
				)->execute([$tid]);
			}
		}
		$pdo->commit();
		header("Location: meme.php?id=$memeId");
		exit();
	} catch (\PDOException $e) {
		throw $e;
	} catch (\Exception $e) {
		throw $e;
	}
}
$pdo = getDbConnection();
$tags = $pdo
	->query(
		"
SELECT
	tag_id,
	name
FROM
	tags
ORDER BY
	name
"
	)
	->fetchAll();
$errors = $_SESSION["upload_errors"] ?? [];
unset($_SESSION["upload_errors"]);
include "memelaboratory.html";
?>
