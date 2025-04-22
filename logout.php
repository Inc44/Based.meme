<?php
session_start();
$_SESSION = [];
session_destroy();
if (isset($_COOKIE["remember_token"])) {
	require_once "db_connect.php";
	try {
		$pdo = getDbConnection();
		$stmt = $pdo->prepare("
DELETE FROM
	user_sessions
WHERE
	session_id = ?
		");
		$stmt->execute([$_COOKIE["remember_token"]]);
	} catch (\PDOException $e) {
		throw $e;
	} catch (\Exception $e) {
		throw $e;
	}
	setcookie("remember_token", "", time() - 3600, "/");
}
header("Location: login.html");
exit();
?>
