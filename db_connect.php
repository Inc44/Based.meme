<?php
define("DB_HOST", "localhost");
define("DB_NAME", "basedmeme");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_CHARSET", "utf8mb4");
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false,
];
$dsn =
	"mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
function getDbConnection(): PDO
{
	global $dsn, $options;
	static $pdo = null;
	if ($pdo === null) {
		try {
			$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		} catch (\PDOException $e) {
			error_log("Database Connection Error: " . $e->getMessage());
			die(
				"Sorry, we don't have any data. Our database is probably nuked. Please try to survive without us."
			);
		}
	}
	return $pdo;
}
?>
