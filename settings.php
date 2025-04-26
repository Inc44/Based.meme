<?php
require_once "db_connect.php";
session_start();
if (!isset($_SESSION["user_id"])) {
	header("Location: login.html");
	exit();
}
$pdo = getDbConnection();
$user_id = $_SESSION["user_id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$avatarMode = $_POST["avatar_mode"] ?? "keep";
	$avatarData = null;
	if ($avatarMode === "file" && !empty($_FILES["avatar_file"]["tmp_name"])) {
		$mime = mime_content_type($_FILES["avatar_file"]["tmp_name"]);
		$avatarData =
			"data:" .
			$mime .
			";base64," .
			base64_encode(
				file_get_contents($_FILES["avatar_file"]["tmp_name"])
			);
	} elseif ($avatarMode === "url") {
		$avatarData = trim($_POST["avatar_url"] ?? "");
	}
	$display_name = trim($_POST["display_name"] ?? "");
	$bio = trim($_POST["bio"] ?? "");
	$location = trim($_POST["location"] ?? "");
	$username = trim($_POST["username"] ?? "");
	$email = trim($_POST["email"] ?? "");
	$password = $_POST["password"] ?? "";
	$birthday = null;
	if (
		!empty($_POST["birthday_year"]) &&
		!empty($_POST["birthday_month"]) &&
		!empty($_POST["birthday_day"])
	) {
		if (
			checkdate(
				$_POST["birthday_month"],
				$_POST["birthday_day"],
				$_POST["birthday_year"]
			)
		) {
			$birthday = sprintf(
				"%04d-%02d-%02d",
				$_POST["birthday_year"],
				$_POST["birthday_month"],
				$_POST["birthday_day"]
			);
		}
	}
	$sex = $_POST["sex"] ?? null;
	$orientation = $_POST["orientation"] ?? null;
	$pronouns = $_POST["pronouns"] ?? null;
	$touch_grass = $_POST["touch-grass"] ?? null;
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
	if ($password !== "") {
		if ($password === "password123") {
			$errors["password"] = "Really? Is that the best you can do?";
		} elseif (strlen($password) < 8) {
			$errors["password"] = "8 characters minimum. Don't be lazy.";
		}
	}
	if (empty($errors["username"]) && empty($errors["email"])) {
		$stmt = $pdo->prepare("
SELECT
	user_id
FROM
	users
WHERE
	handle =?
	AND user_id <>?
		");
		$stmt->execute([$username, $user_id]);
		if ($stmt->fetch()) {
			$errors["username"] = "Username taken. Be more original.";
		}
		$stmt = $pdo->prepare("
SELECT
	user_id
FROM
	users
WHERE
	email =?
	AND user_id <>?
		");
		$stmt->execute([$email, $user_id]);
		if ($stmt->fetch()) {
			$errors["email"] = "Email already in use. Nice try.";
		}
	}
	if (empty($errors)) {
		$set =
			"handle=?, email=?, display_name=?, bio=?, birthday=?, sex=?, orientation=?, pronouns=?, touch_grass=?, location=?";
		$parameters = [
			$username,
			$email,
			$display_name,
			$bio,
			$birthday,
			$sex,
			$orientation,
			$pronouns,
			$touch_grass,
			$location,
		];
		if ($avatarData !== null) {
			$set .= ", avatar=?";
			$parameters[] = $avatarData;
		}
		if ($password) {
			$set .= ", password_hash=?";
			$parameters[] = password_hash($password, PASSWORD_BCRYPT);
		}
		$parameters[] = $user_id;
		$stmt = $pdo->prepare("
UPDATE
	users
SET
	$set
WHERE
	user_id =?
		");
		$stmt->execute($parameters);
		$_SESSION["handle"] = $username;
		header("Location: profile.php?handle={$username}");
		exit();
	}
	$_SESSION["settings_errors"] = $errors;
	$_SESSION["settings_data"] = $_POST;
	header("Location: settings.php");
	exit();
}
$stmt = $pdo->prepare("
SELECT
	*
FROM
	users
WHERE
	user_id =?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$sexOptions = [
	"male" => "Male",
	"female" => "Female",
	"prefer-not" => "Prefer not to say",
	"other" => "Other",
	"yes" => "Yes, please",
	"no" => "No, thanks",
	"error" => "Sex not found",
];
$orientationOptions = [
	"straight" => "Straight",
	"gay" => "Gay",
	"lesbian" => "Lesbian",
	"bisexual" => "Bisexual",
	"asexual" => "Asexual",
	"pansexual" => "Pansexual",
	"attack" => "Attack helicopter",
	"portrait" => "Portrait mode",
];
$pronounsOptions = [
	"he-him" => "he/him",
	"she-her" => "she/her",
	"they-them" => "they/them",
	"ze-zir" => "ze/zir",
	"any" => "any pronouns",
	"burger" => "burger/king",
	"sigma" => "sigma/male",
	"cringe" => "cringe/based",
	"copy" => "copy/paste",
	"who" => "who/asked",
];
$grassOptions = [
	"today" => "Today",
	"week" => "This week",
	"month" => "This month",
	"year" => "This year",
	"never" => "What's grass?",
	"yesterday" => "Yesterday (lying)",
	"decade" => "Decades ago",
	"1000" => "I haven't heard of it in 1,000 years",
	"10000" => "I haven't seen it for 10,000 years",
	"minecraft" => "In Minecraft",
];
include "settings.html";
?>
