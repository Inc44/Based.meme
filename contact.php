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
	$errors = [
		"name" => empty(trim($_POST["name"]))
			? "Your Internet Alias is required."
			: null,
		"email" => !filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)
			? "A valid email is required."
			: null,
		"category" => empty(trim($_POST["category"]))
			? "Please select a category (What's your damage?)."
			: null,
		"message" => empty(trim($_POST["message"]))
			? "The message (Spill the Tea) cannot be empty."
			: null,
		"captcha" => !preg_match(
			'/^(?:piece of shit|[A-Za-z]{5}\s[A-Za-z]{2}\s[A-Za-z]{4})$/',
			trim(strtolower($_POST["captcha"]))
		)
			? "Incorrect captcha. Are you a normie?"
			: null,
	];
	$message = trim($_POST["message"]);
	if (stripos($message, "my problem") !== false) {
		$errors["my_problem"] =
			"Make sure that this is our problem; if it is your problem, fuck you.";
	}
	$reporter_id = $_SESSION["user_id"] ?? 0;
	if ($reporter_id && !array_filter($errors)) {
		try {
			$pdo = getDbConnection();
			$stmt = $pdo->prepare("
SELECT
	is_admin
FROM
	users
WHERE
	user_id = ?
LIMIT
	1
			");
			$stmt->execute([$reporter_id]);
			if ($stmt->fetchColumn()) {
				$errors["admin"] = "Why are you contacting yourself, admin?";
			}
		} catch (\PDOException $e) {
			throw $e;
			header("Location: status.php");
			exit();
		} catch (\Exception $e) {
			throw $e;
		}
	}
	if ($error = array_filter($errors)) {
		$error_key = array_key_first($error);
		$_SESSION["404_code"] =
			$error_key === "admin"
				? "Bro WTF?"
				: ($error_key === "my_problem"
					? "We don't care"
					: "You failed");
		$_SESSION["404_message"] = reset($error);
		header("Location: 404.php");
		exit();
	}
	$name = trim($_POST["name"]);
	$email = trim($_POST["email"]);
	$reason = trim($_POST["category"]);
	$report_id = yid();
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
		header("Location: status.php");
		exit();
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
		if ($user = $stmt->fetch()) {
			$name = htmlspecialchars($user["handle"]);
			$email = htmlspecialchars($user["email"]);
		}
	} catch (\PDOException $e) {
		throw $e;
		header("Location: status.php");
		exit();
	} catch (\Exception $e) {
		throw $e;
	}
}
include "contact.html";
?>
