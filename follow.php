<?php
require_once "db_connect.php";
session_start();
if (!isset($_SESSION["user_id"]) || !isset($_POST["user_id"])) {
	header("Location: login.html");
	exit();
}
$follower_id = $_SESSION["user_id"];
$following_id = $_POST["user_id"];
if ($follower_id === $following_id) {
	header("Location: profile.php?handle=" . $_SESSION["handle"]);
	exit();
}
try {
	$pdo = getDbConnection();
	$stmt = $pdo->prepare("
SELECT
	COUNT(*)
FROM
	follows
WHERE
	follower_id = ?
	AND following_id = ?
	");
	$stmt->execute([$follower_id, $following_id]);
	$isFollowing = $stmt->fetchColumn() > 0;
	if ($isFollowing) {
		$stmt = $pdo->prepare("
DELETE FROM
	follows
WHERE
	follower_id = ?
	AND following_id = ?
		");
		$stmt->execute([$follower_id, $following_id]);
	} else {
		$stmt = $pdo->prepare("
INSERT INTO
	follows (follower_id, following_id)
VALUES
	(?, ?)
		");
		$stmt->execute([$follower_id, $following_id]);
	}
	$stmt = $pdo->prepare("
SELECT
	handle
FROM
	users
WHERE
	user_id = ?
	");
	$stmt->execute([$following_id]);
	$handle = $stmt->fetchColumn();
	header("Location: profile.php?handle=" . $handle);
	exit();
} catch (\PDOException $e) {
	throw $e;
	header("Location: status.php");
	exit();
} catch (\Exception $e) {
	throw $e;
	header("Location: index.php");
	exit();
}
?>
