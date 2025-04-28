<?php
require_once "db_connect.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$username = trim($_POST["username"] ?? "");
	$password = $_POST["password"] ?? "";
	$remember = isset($_POST["remember"]);
	if (empty($username) || empty($password)) {
		$_SESSION["404_message"] =
			"Fields can't be empty. Imagine forgetting that.";
		header("Location: 404.php");
		exit();
	}
	try {
		$pdo = getDbConnection();
		if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$stmt = $pdo->prepare("
SELECT
	user_id,
	handle,
	password_hash
FROM
	users
WHERE
	email = ?
			");
		} else {
			$stmt = $pdo->prepare("
SELECT
	user_id,
	handle,
	password_hash
FROM
	users
WHERE
	handle = ?
			");
		}
		$stmt->execute([$username]);
		$user = $stmt->fetch();
		if ($user && password_verify($password, $user["password_hash"])) {
			$_SESSION["user_id"] = $user["user_id"];
			$_SESSION["handle"] = $user["handle"];
			$update = $pdo->prepare("
UPDATE
	users
SET
	last_login = CURRENT_TIMESTAMP
WHERE
	user_id = ?
			");
			$update->execute([$user["user_id"]]);
			$session_id = substr(bin2hex(random_bytes(11)), 0, 11);
			$ip = $_SERVER["REMOTE_ADDR"] ?? "";
			$agent = $_SERVER["HTTP_USER_AGENT"] ?? "";
			$stmt = $pdo->prepare("
INSERT INTO
	user_sessions (session_id, user_id, ip_address, user_agent)
VALUES
	(?, ?, ?, ?)
			");
			$stmt->execute([$session_id, $user["user_id"], $ip, $agent]);
			if ($remember) {
				setcookie(
					"remember_token",
					$session_id,
					time() + 30 * 24 * 60 * 60,
					"/",
					"",
					true,
					true
				);
			}
			header("Location: index.php");
			exit();
		} else {
			$_SESSION["404_message"] = "Brain Not Found";
			header("Location: 404.php");
			exit();
		}
	} catch (\PDOException $e) {
		throw $e;
		header("Location: status.php");
		exit();
	} catch (\Exception $e) {
		$_SESSION["404_message"] = "Login system broke. Blame the intern.";
		throw $e;
		header("Location: 404.php");
		exit();
	}
} else {
	header("Location: login.html");
	exit();
}
?>
