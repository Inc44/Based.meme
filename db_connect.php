<?php
define("DB_HOST", "localhost");
define("DB_NAME", "basedmeme");
define("DB_USER", "root");
define("DB_PASS", getenv("ADMIN_PASSWORD"));
define("DB_CHARSET", "utf8mb4");
$options = [
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::ATTR_EMULATE_PREPARES => false,
];
$dsn =
	"mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
function getDbConnection(): ?PDO
{
	global $dsn, $options;
	static $pdo = null;
	if ($pdo === null) {
		try {
			$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
		} catch (\PDOException $e) {
			throw $e;
			header("Location: status.php");
			exit();
		} catch (\Exception $e) {
			throw $e;
		}
	}
	return $pdo;
}
?>
