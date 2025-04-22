<?php
require_once "db_connect.php";
function generate_connection_params_html(): string
{
	return sprintf(
		"<pre>Host: %s\nDatabase: %s\nUser: %s</pre>",
		htmlspecialchars(DB_HOST),
		htmlspecialchars(DB_NAME),
		htmlspecialchars(DB_USER)
	);
}

try {
	$connection = getDbConnection();
	$statusData = [
		"taglineIcon" => "✅",
		"taglineText" => "Database Connection Successful!",
		"subtitleText" => "Successfully pinged the meme mainframe.",
		"cardClass" => "db-card db-success-card",
		"detailsHtml" => sprintf(
			"<div class='details-container'><h4>Connection Details:</h4>%s<p>It looks like the database hamster is awake and running!</p></div>",
			generate_connection_params_html()
		),
	];
} catch (\PDOException $e) {
	$statusData = [
		"taglineIcon" => "❌",
		"taglineText" => "Database Connection Failed!",
		"subtitleText" => "Error connecting to the meme mainframe.",
		"cardClass" => "db-card db-error-card",
		"detailsHtml" => sprintf(
			"<div class='details-container'><h4>Connection Attempt Details:</h4>%s<p>Oops! The database hamster fell off its wheel. Please try again later, as we are genuinely sorry that we don't have any data and our database is probably nuked. Please try to survive without us while we're working on it.</p></div>",
			generate_connection_params_html()
		),
	];
} catch (\Exception $e) {
	$statusData = [
		"taglineIcon" => "🔥",
		"taglineText" => "Unexpected Error During Test!",
		"subtitleText" =>
			"Something went very wrong. Our production may be on fire. Our bad!",
		"cardClass" => "db-card db-fire-card",
		"detailsHtml" => sprintf(
			"<div class='details-container'><h4>Error Details:</h4><pre>" .
				htmlspecialchars($e->getMessage()) .
				"</pre></div>"
		),
	];
}
extract($statusData);
require "status.html";
?>
