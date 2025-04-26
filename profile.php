<?php
require_once "db_connect.php";
session_start();
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
	TIMESTAMPDIFF(YEAR, joined_at, NOW()) AS years_registered,
	TIMESTAMPDIFF(DAY, joined_at, NOW()) AS days_registered,
	TIMESTAMPDIFF(HOUR, joined_at, NOW()) AS hours_registered
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
	COALESCE(SUM(dislike_count), 0) AS cringe_count,
	COALESCE(SUM(upvote_count), 0) AS based_count,
	COALESCE(SUM(comment_count), 0) AS comments,
	(
		SELECT
			COUNT(*)
		FROM
			follows
		WHERE
			following_id = ?
	) AS followers
FROM
	memes
WHERE
	user_id = ?
	AND status = 'published'
	");
	$stmt->execute([$user["user_id"], $user["user_id"]]);
	$stats = $stmt->fetch();
	if ($viewerId) {
		$stmt = $pdo->prepare("
SELECT
	COUNT(*)
FROM
	follows
WHERE
	follower_id = ?
	AND following_id = ?
		");
		$stmt->execute([$viewerId, $user["user_id"]]);
		$isFollowing = $stmt->fetchColumn() > 0;
	} else {
		$isFollowing = false;
	}
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
	date("md", strtotime($user["birthday"])) === date("md") &&
		($badges[] = ["icon" => "🎉", "label" => "Happy Birthday!"]);
	$user["orientation"] === "attack" &&
		($badges[] = ["icon" => "♊", "label" => "Call 911"]);
	switch (true) {
		case $user["pronouns"] == "burger":
			$badges[] = [
				"icon" => "🍔",
				"label" => "Kotleta inside", // Hamburger; I got the burgers in the back
			];
			break;
		case $user["pronouns"] == "sigma":
			$badges[] = [
				"icon" => "🐺",
				"label" => "Auf",
			];
			break;

		case $user["pronouns"] == "cringe":
			$badges[] = [
				"icon" => "🤡",
				"label" => "Clown",
			];
			break;
		case $user["pronouns"] == "who":
			$badges[] = [
				"icon" => "🤔",
				"label" => "Who cares?",
			];
			break;
	}
	switch (true) {
		case in_array(
			$user["touch_grass"],
			["never", "1000", "10000", "minecraft"],
			true
		):
			$badges[] = [
				"icon" => "🌚",
				"label" => "Needs Vitamin D",
			];
			break;
		case in_array(
			$user["touch_grass"],
			["week", "month", "year", "decade"],
			true
		):
			$badges[] = [
				"icon" => "🍀",
				"label" => "Touch Grass Reminder",
				"description" => "Seriously, go outside once in a while",
			];
			break;
	}
	$user["location"] &&
		($badges[] = ["icon" => "📍", "label" => "Geoguessed"]);
	$user["is_verified"] &&
		($badges[] = ["icon" => "✅", "label" => "Verified"]);
	switch (true) {
		case $user["is_private"]:
			$badges[] = [
				"icon" => "🔒",
				"label" => "Private",
				"description" =>
					"This profile is locked. Only memes are public.",
			];
			break;
		default:
			$badges[] = ["icon" => "🌐", "label" => "Public"];
			break;
	}
	$user["is_admin"] && ($badges[] = ["icon" => "🛡️", "label" => "Admin"]);
	$user["is_banned"] && ($badges[] = ["icon" => "🚫", "label" => "Banned"]);
	switch (true) {
		case date("md", strtotime($user["joined_at"])) === date("md"):
			$badges[] = [
				"icon" => "🎂",
				"label" => "Today is special",
			];
			break;
		case $user["years_registered"] > 1:
			$badges[] = [
				"icon" => "🎖️",
				"label" => $user["years_registered"] . "y Veteran",
			];
			break;
		case $user["days_registered"] >= 90:
			$badges[] = [
				"icon" => "📜",
				"label" => "Internet Historian",
				"description" => "Remembers memes from 3 whole months ago",
			];
			break;
		case $user["days_registered"] >= 9:
			$badges[] = ["icon" => "📅", "label" => "As time goes by"];
			break;
		case $user["hours_registered"] >= 1000:
			$badges[] = [
				"icon" => "⏱️",
				"label" => "1000h Wasted",
				"description" => "Spent way too much time scrolling dank memes",
			];
			break;
	}
	switch (true) {
		case $stats["followers"] >= 1000:
			$badges[] = [
				"icon" => "💪",
				"label" => "Dungeon Master",
				"description" => "You must be doing something right",
			];
			break;
		case $stats["followers"] >= 1:
			$badges[] = [
				"icon" => "☠️",
				"label" => "Welcome to the club",
				"description" => "Got your first follower!",
			];
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
			$badges[] = ["icon" => "🐐", "label" => "Legend"];
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
				"icon" => "🫵",
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
				"icon" => "🦾",
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
				"icon" => "🏋",
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
			$badges[] = ["icon" => "🤑", "label" => "Adorable"];
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
				"icon" => "📎",
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
	$stats["cringe_count"] >= 228 &&
		($badges[] = [
			"icon" => "💩",
			"label" => "Professional Shitposter",
			"description" => "Quality? Who needs that when you have quantity",
		]);
	switch (true) {
		case $stats["based_count"] >= 9000:
			$badges[] = [
				"icon" => "🥇",
				"label" => "It's over 9000",
			];
			break;
		case $stats["based_count"] >= 1337:
			$badges[] = [
				"icon" => "👑",
				"label" => "Based Lord",
				"description" =>
					"Your content has been deemed acceptably based",
			];
			break;
		case $stats["based_count"] >= 420:
			$badges[] = [
				"icon" => "😶‍🌫️",
				"label" => "Certified Based",
			];
			break;
		case $stats["based_count"] >= 100:
			$badges[] = [
				"icon" => "💯",
				"label" => "Century Poster",
			];
			break;
		default:
			$badges[] = ["icon" => "🌱", "label" => "Sprout"];
			break;
	}
	if ($stats["based_count"] >= 42 && $stats["cringe_count"] >= 42) {
		$badges[] = [
			"icon" => "🔥",
			"label" => "Controversial Creator",
			"description" =>
				"You either love them or hate them, there is no in-between",
		];
	}
	switch (true) {
		case $stats["comments"] >= 50000:
			$badges[] = [
				"icon" => "💵",
				"label" => "You bought them",
			];
			break;
		case $stats["comments"] >= 500:
			$badges[] = [
				"icon" => "🫰",
				"label" => "Social Squeezer",
				"description" => "Pinching out those top‐tier replies!",
			];
			break;
	}
	if ($avgSpiciness >= 0.84) {
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
} catch (\Exception $e) {
	throw $e;
	exit();
}
include "profile.html";
?>
