<?php
$phrases = [
	"ERROR 404 : This meme has been deleted by the council.",
	"You ventured too deep. Turn back, traveler.",
	"Oops. This page ghosted us.",
	"You weren't supposed to see this. Now we have to kill you.",
	"Page not found... but your sense of humor is.",
	"This page went out for milk and never came back.",
	"Bro, even Shrek can't find this one.",
	"404 : Vibe check failed.",
	"Nothing here but pain and broken links.",
	"Congrats, you found our secret hideout. Just kidding. It's broken.",
];

$errorMessage = $phrases[array_rand($phrases)];
include "404.html";
?>
