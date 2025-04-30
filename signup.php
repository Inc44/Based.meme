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
	$username = trim($_POST["username"] ?? "");
	$email = trim($_POST["email"] ?? "");
	$password = $_POST["password"] ?? "";
	$confirm_password = $_POST["confirm-password"] ?? "";
	$secret_question = $_POST["secret-question"] ?? "";
	$secret_answer = trim($_POST["secret-answer"] ?? "");
	$birthday_day = !empty($_POST["birthday-day"])
		? (int) $_POST["birthday-day"]
		: null;
	$birthday_month = !empty($_POST["birthday-month"])
		? (int) $_POST["birthday-month"]
		: null;
	$birthday_year = !empty($_POST["birthday-year"])
		? (int) $_POST["birthday-year"]
		: null;
	$sex = $_POST["sex"] ?? null;
	$orientation = $_POST["orientation"] ?? null;
	$pronouns = $_POST["pronouns"] ?? null;
	$touch_grass = $_POST["touch-grass"] ?? null;
	$meme_knowledge = trim($_POST["meme-knowledge"] ?? "");
	$oath_checked = isset($_POST["oath"]);
	$terms_checked = isset($_POST["terms"]);
	$errors = [];
	if (empty($username)) {
		$errors["username"] = "Username? Yeah, we kinda need that.";
	} elseif (strlen($username) > 32) {
		$errors["username"] = "32 characters max, Shakespeare. Chill.";
	} elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
		$errors["username"] = "Letters, numbers, underscores. Don't get cute.";
	}
	if (empty($email)) {
		$errors["email"] = "Email. Now. Don't make me ask twice.";
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors["email"] = "That's not an email, genius.";
	} elseif (strlen($email) > 254) {
		$errors["email"] = "Email is too long. Shorten it.";
	}
	if (empty($password)) {
		$errors["password"] = "Password. You forgot the password. Seriously?";
	} elseif ($password === "password123") {
		$errors["password"] = "Really? Is that the best you can do?";
	} elseif (strlen($password) < 8) {
		$errors["password"] = "8 characters minimum. Don't be lazy.";
	} elseif (!preg_match('/^[\x20-\x7E]+$/', $password)) {
		$errors["password"] =
			"Password must contain only typable ASCII characters.";
	}
	if ($password !== $confirm_password) {
		$errors["confirm-password"] = "Passwords don't match. Try harder.";
	}
	if (empty($secret_question)) {
		$errors["secret-question"] = "Pick a secret question, spy.";
	}
	if (empty($secret_answer)) {
		$errors["secret-answer"] = "Secret answer. It's not optional, champ.";
	}
	if (!$oath_checked) {
		$errors["oath"] = "You must take the oath!";
	}
	if (!$terms_checked) {
		$errors["terms"] = "Accept the terms (even if you didn't read them).";
	}
	if (empty($errors["username"]) && empty($errors["email"])) {
		try {
			$pdo = getDbConnection();
			$stmt = $pdo->prepare("
SELECT
	user_id
FROM
	users
WHERE
	handle = ?
			");
			$stmt->execute([$username]);
			if ($stmt->fetch()) {
				$errors["username"] = "Username taken. Be more original.";
			}
			$stmt = $pdo->prepare("
SELECT
	user_id
FROM
	users
WHERE
	email = ?
			");
			$stmt->execute([$email]);
			if ($stmt->fetch()) {
				$errors["email"] = "Email already in use. Nice try.";
			}
		} catch (\PDOException $e) {
			$errors["db"] = "Validation broke. Blame the server. Try again.";
			throw $e;
			header("Location: status.php");
			exit();
		} catch (\Exception $e) {
		}
	}
	if (empty($errors)) {
		$hashed_password = password_hash($password, PASSWORD_BCRYPT);
		$hashed_secret_answer = password_hash($secret_answer, PASSWORD_BCRYPT);
		$birthday = null;
		if ($birthday_year && $birthday_month && $birthday_day) {
			if (checkdate($birthday_month, $birthday_day, $birthday_year)) {
				$birthday = sprintf(
					"%04d-%02d-%02d",
					$birthday_year,
					$birthday_month,
					$birthday_day
				);
			}
		}
		$sql = "
INSERT INTO
	users (
		handle,
		email,
		password_hash,
		birthday,
		sex,
		orientation,
		pronouns,
		touch_grass,
		meme_knowledge,
		secret_question,
		secret_answer_hash
	)
VALUES
	(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
		";
		try {
			$pdo = getDbConnection();
			$stmt = $pdo->prepare($sql);
			$stmt->execute([
				$username,
				$email,
				$hashed_password,
				$birthday,
				$sex,
				$orientation,
				$pronouns,
				$touch_grass,
				empty($meme_knowledge) ? null : $meme_knowledge,
				$secret_question,
				$hashed_secret_answer,
			]);
			$userId = $pdo->lastInsertId();
			$ip = $_SERVER["REMOTE_ADDR"] ?? "";
			$agent = $_SERVER["HTTP_USER_AGENT"] ?? "";
			$stmt = $pdo->prepare("
INSERT INTO
	privacy_consents (
		consent_id,
		user_id,
		consent_type,
		is_granted,
		ip_address,
		user_agent
	)
VALUES
	(?, ?, ?, TRUE, ?, ?)
			");
			$consentTypes = [
				"terms",
				"privacy",
				"cookies",
				"marketing",
				"third_party",
			];
			foreach ($consentTypes as $type) {
				$stmt->execute([yid(), $userId, $type, $ip, $agent]);
			}
			header("Location: login.html?signup=success");
			exit();
		} catch (\PDOException $e) {
			$_SESSION["signup_error"] =
				"Registration failed. Server's drunk. Retry.";
			throw $e;
			header("Location: status.php");
			exit();
		} catch (\Exception $e) {
			header("Location: signup.html");
			exit();
		}
	} else {
		$_SESSION["signup_errors"] = $errors;
		$_SESSION["signup_data"] = $_POST;
		header("Location: signup.html");
		exit();
	}
} else {
	header("Location: signup.html");
	exit();
}
?>
