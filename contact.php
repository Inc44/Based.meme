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
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$name = trim($_POST["name"] ?? "");
	$email = trim($_POST["email"] ?? "");
	$reason = $_POST["category"] ?? "other";
	$message = trim($_POST["message"] ?? "");
	$report_id = yid();
	$reporter_id = $_SESSION["user_id"] ?? 0;
	$content_type = $_POST["content_type"] ?? "user";
	$content_id = $_POST["content_id"] ?? yid();
	$details = "Submitted via contact form by: $name ($email)\n\n$message";
	try {
		$pdo = getDbConnection();
		$stmt = $pdo->prepare("
INSERT INTO
	reports (
		report_id,
		reporter_id,
		content_type,
		content_id,
		reason,
		details
	)
VALUES
	(?, ?, ?, ?, ?, ?)
		");
		$stmt->execute([
			$report_id,
			$reporter_id,
			$content_type,
			$content_id,
			$reason,
			$details,
		]);
		$_SESSION["contact_success"] =
			"Report submitted successfully. We'll look into it (maybe).";
	} catch (\PDOException $e) {
		throw $e;
	} catch (\Exception $e) {
		throw $e;
	}
	header("Location: contact.php");
	exit();
}
$name = "";
$email = "";
$category = $_GET["category"] ?? "bug";
if (isset($_SESSION["user_id"])) {
	try {
		$pdo = getDbConnection();
		$stmt = $pdo->prepare("
SELECT
	handle,
	email
FROM
	users
WHERE
	user_id = ?
		");
		$stmt->execute([$_SESSION["user_id"]]);
		$user = $stmt->fetch();
		if ($user) {
			$name = htmlspecialchars($user["handle"]);
			$email = htmlspecialchars($user["email"]);
		}
	} catch (\PDOException $e) {
		throw $e;
	} catch (\Exception $e) {
		throw $e;
	}
}
include "contact.html";
?>
