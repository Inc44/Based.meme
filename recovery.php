<?php
require_once "db_connect.php";
session_start();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$name_or_email = trim($_POST["name-or-email"]);
	$secret_question = trim($_POST["secret-question"]);
	$secret_answer = trim($_POST["secret-answer"]);
	$captcha = trim(strtolower($_POST["captcha"]));
	$errors = [
		"name_or_email" => empty($name_or_email)
			? "Username or Email is required."
			: null,
		"secret_question" => empty($secret_question)
			? "Secret Question is required."
			: null,
		"secret_answer" => empty($secret_answer)
			? "Secret Answer is required."
			: null,
		"captcha" => !preg_match(
			'/^(?:burgers|[A-Za-z]{7})$/',
			trim(strtolower($captcha))
		)
			? "Incorrect captcha. Hint: Whopper Whopper Whopper Whopper..."
			: null,
	];
	if ($error = array_filter($errors)) {
		$error_key = array_key_first($error);
		$_SESSION["404_code"] = "You're a failure";
		$_SESSION["404_message"] = reset($error);
		header("Location: 404.php");
		exit();
	}
	try {
		$pdo = getDbConnection();
		if (filter_var($name_or_email, FILTER_VALIDATE_EMAIL)) {
			$stmt = $pdo->prepare("
SELECT
	user_id,
	handle,
	secret_question,
	secret_answer_hash
FROM
	users
WHERE
	email = ?
LIMIT
	1
            ");
		} else {
			$stmt = $pdo->prepare("
SELECT
	user_id,
	handle,
	secret_question,
	secret_answer_hash
FROM
	users
WHERE
	handle = ?
LIMIT
	1
            ");
		}
		$stmt->execute([$name_or_email]);
		$user = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($user) {
			if (
				$user["secret_question"] === $secret_question &&
				password_verify($secret_answer, $user["secret_answer_hash"])
			) {
				$_SESSION["404_code"] = "Emotional damage";
				$_SESSION["404_message"] =
					"Identity verified! You may now proceed to login or reset your password (if that feature existed).";
				header("Location: 404.php");
				exit();
			} else {
				$_SESSION["404_code"] = "Womp, womp";
				$_SESSION["404_message"] =
					"Incorrect secret question or answer. Check your memory banks.";
				header("Location: 404.php");
				exit();
			}
		} else {
			$_SESSION["404_message"] =
				"No user found for that username or email. Did you mistype?";
			header("Location: 404.php");
			exit();
		}
	} catch (\PDOException $e) {
		throw $e;
		header("Location: status.php");
		exit();
	} catch (\Exception $e) {
		throw $e;
	}
}
?>
