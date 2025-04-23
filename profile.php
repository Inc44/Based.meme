<?php
session_start();
require_once "db_connect.php";
function addOrdinalSuffix(int $day): string
{
	$remainder = $day % 100;
	if ($remainder >= 11 && $remainder <= 13) {
		return $day . "th";
	}
	return $day .
		["th", "st", "nd", "rd", "th", "th", "th", "th", "th", "th"][$day % 10];
}
function prettyDate(?string $date): string
{
	if (!$date) {
		return "";
	}
	$timestamp = strtotime($date);
	return addOrdinalSuffix((int) date("j", $timestamp)) .
		" " .
		date("F Y", $timestamp);
}
function lastLogin(string $timestamp): string
{
	$seconds = time() - strtotime($timestamp);
	return $seconds < 600
		? "recently"
		: ($seconds < 3600
			? "today"
			: ($seconds < 86400
				? "yesterday"
				: ($seconds < 604800
					? "last week"
					: ($seconds < 2_592_000
						? "last month"
						: ($seconds < 31_536_000
							? "last year"
							: "since the beginning of time")))));
}
function resolveLabel(?string $raw, array $labels): ?string
{
	return $raw === null || $raw === "" ? null : $labels[$raw] ?? ucfirst($raw);
}
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
$handle = $_GET["handle"] ?? null;
if (!$handle) {
	if (isset($_SESSION["handle"])) {
		header(
			"Location: profile.php?handle=" . urlencode($_SESSION["handle"])
		);
		exit();
	}
	header("Location: login.html");
	exit();
}
try {
	$pdo = getDbConnection();
	$stmt = $pdo->prepare("
SELECT
	*,
	TIMESTAMPDIFF(YEAR, joined_at, NOW()) AS years_registered
FROM
	users
WHERE
	handle = ?
	");
	$stmt->execute([$handle]);
	$user = $stmt->fetch();
	if (!$user) {
		header("Location: 404.html");
		exit();
	}
	$viewerId = $_SESSION["user_id"] ?? null;
	$isOwner = $viewerId && (int) $viewerId === (int) $user["user_id"];
	$isPrivate = (int) $user["is_private"] === 1 && !$isOwner;
	$stmt = $pdo->prepare("
SELECT
	COUNT(*) AS meme_count,
	COALESCE(SUM(like_count), 0) AS likes,
	COALESCE(SUM(comment_count), 0) AS comments
FROM
	memes
WHERE
	user_id = ?
	AND status = 'published'
	");
	$stmt->execute([$user["user_id"]]);
	$stats = $stmt->fetch();
	$visibilityFilter = $isOwner ? "" : " AND visibility='public'";
	$stmt = $pdo->prepare("
SELECT
	AVG(spicyness)
FROM
	memes
WHERE
	user_id = ?
	AND status = 'published' $visibilityFilter
	");
	$stmt->execute([$user["user_id"]]);
	$avgSpiciness = number_format($stmt->fetchColumn() ?? 0, 2);
	$stmt = $pdo->prepare("
SELECT
	meme_id,
	slug,
	title,
	media_url,
	like_count,
	comment_count,
	spicyness
FROM
	memes
WHERE
	user_id = ?
	AND status = 'published' $visibilityFilter
ORDER BY
	created_at DESC
	");
	$stmt->execute([$user["user_id"]]);
	$memes = $stmt->fetchAll();
	$stmt = $pdo->prepare("
SELECT
	t.name
FROM
	tags AS t
	JOIN meme_tags AS mt ON mt.tag_id = t.tag_id
WHERE
	mt.meme_id = ?
	");
	foreach ($memes as $i => $meme) {
		$stmt->execute([$meme["meme_id"]]);
		$memes[$i]["tags"] = $stmt->fetchAll(PDO::FETCH_COLUMN);
	}
	$badges = [];
	$user["is_admin"] && ($badges[] = ["icon" => "👑", "label" => "Admin"]);
	$user["is_verified"] &&
		($badges[] = ["icon" => "✔️", "label" => "Verified"]);
	if ($user["years_registered"] >= 1) {
		$badges[] = [
			"icon" => "📅",
			"label" => $user["years_registered"] . "y Veteran",
		];
	}
	switch (true) {
		case $stats["likes"] >= 100000:
			$badges[] = [
				"icon" => "💎",
				"label" => "Maximum Rizz - 100K+ Likes",
			];
			break;
		case $stats["likes"] >= 50000:
			$badges[] = ["icon" => "🤴", "label" => "Gigachad"];
			break;
		case $stats["likes"] >= 25000:
			$badges[] = ["icon" => "🌟", "label" => "Celebrity"];
			break;
		case $stats["likes"] >= 10000:
			$badges[] = ["icon" => "🔥", "label" => "Adorable"];
			break;
		case $stats["likes"] >= 5000:
			$badges[] = [
				"icon" => "👍",
				"label" => "Almost Famous",
			];
			break;
		case $stats["likes"] >= 1000:
			$badges[] = ["icon" => "⭐", "label" => "Rising Star"];
			break;
		case $stats["likes"] >= 500:
			$badges[] = [
				"icon" => "😊",
				"label" => "Friendly Face",
			];
			break;
		case $stats["likes"] >= 100:
			$badges[] = [
				"icon" => "💯",
				"label" => "A Bit Noticed",
			];
			break;
		case $stats["likes"] >= 50:
			$badges[] = [
				"icon" => "😉",
				"label" => "Barely Buzzing",
			];
			break;
		case $stats["likes"] >= 10:
			$badges[] = ["icon" => "👶", "label" => "Just Started"];
			break;
	}
	switch (true) {
		case $stats["meme_count"] >= 10000:
			$badges[] = [
				"icon" => "🏆",
				"label" => "SlotAchieveName_0020: NULL",
			];
			break;
		case $stats["meme_count"] >= 7500:
			$badges[] = ["icon" => "👑", "label" => "Legend"];
			break;
		case $stats["meme_count"] >= 5000:
			$badges[] = [
				"icon" => "💡",
				"label" => "Enlightened",
			];
			break;
		case $stats["meme_count"] >= 3500:
			$badges[] = ["icon" => "💧", "label" => "Blue Blood"];
			break;
		case $stats["meme_count"] >= 2500:
			$badges[] = ["icon" => "🦸", "label" => "Hero"];
			break;
		case $stats["meme_count"] >= 2000:
			$badges[] = ["icon" => "🤪", "label" => "Maniac"];
			break;
		case $stats["meme_count"] >= 1500:
			$badges[] = ["icon" => "🍽️", "label" => "Gourmet"];
			break;
		case $stats["meme_count"] >= 1234:
			$badges[] = [
				"icon" => "🔢",
				"label" => "One, Two, Three, Four",
			];
			break;
		case $stats["meme_count"] >= 1000:
			$badges[] = [
				"icon" => "🚫",
				"label" => "Point of No Return",
			];
			break;
		case $stats["meme_count"] >= 888:
			$badges[] = [
				"icon" => "♾️",
				"label" => "Endless Journey",
			];
			break;
		case $stats["meme_count"] >= 750:
			$badges[] = [
				"icon" => "💪",
				"label" => "Not Even My Final Form",
			];
			break;
		case $stats["meme_count"] >= 666:
			$badges[] = [
				"icon" => "😈",
				"label" => "Satanic Limit?",
			];
			break;
		case $stats["meme_count"] >= 500:
			$badges[] = [
				"icon" => "⏳",
				"label" => "Lots of Free Time?",
			];
			break;
		case $stats["meme_count"] >= 400:
			$badges[] = [
				"icon" => "🎩",
				"label" => "Something to Brag About",
			];
			break;
		case $stats["meme_count"] >= 300:
			$badges[] = ["icon" => "🏎️", "label" => "Speedrunner"];
			break;
		case $stats["meme_count"] >= 200:
			$badges[] = ["icon" => "🚶", "label" => "Easy Stroll"];
			break;
		case $stats["meme_count"] >= 150:
			$badges[] = [
				"icon" => "😎",
				"label" => "Carefree Memer",
			];
			break;
		case $stats["meme_count"] >= 100:
			$badges[] = [
				"icon" => "💪",
				"label" => "My Powers Grow",
			];
			break;
		case $stats["meme_count"] >= 50:
			$badges[] = [
				"icon" => "🚀",
				"label" => "This Is Just the Beginning",
			];
			break;
		case $stats["meme_count"] >= 15:
			$badges[] = ["icon" => "👋", "label" => "Welcome!"];
			break;
	}
	if ($avgSpiciness >= 0.8) {
		$badges[] = ["icon" => "🌶️", "label" => "Spice Lord"];
	}
	$fullBio = $user["bio"] ?? "";
	$needsToggle = mb_strlen($fullBio) > 69;
	$shortBio = $needsToggle ? mb_substr($fullBio, 0, 69) . "…" : $fullBio;
	$sexLabel = resolveLabel($user["sex"], $sexOptions);
	$orientationLabel = resolveLabel($user["orientation"], $orientationOptions);
	$pronounsLabel = resolveLabel($user["pronouns"], $pronounsOptions);
	$grassLabel = resolveLabel($user["touch_grass"], $grassOptions);
} catch (\PDOException $e) {
	throw $e;
	exit();
}
include "profile.html";
?>
