<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Vérifie si un fichier a été envoyé
	if (isset($_FILES["profileImage"])) {
		$file_name = $_FILES["profileImage"]["name"];
		echo "Fichier téléchargé : " . $file_name . "<br>";
	}
	// Récupérer les autres données du formulaire
	$description = isset($_POST["description"])
		? $_POST["description"]
		: "Aucune description";
	$spiciness = isset($_POST["spiciness"])
		? $_POST["spiciness"]
		: "Non défini";
	echo "Description : " . $description . "<br>";
	echo "Spiciness : " . $spiciness . "<br>";
	// Récupérer les tags sélectionnés
	$tags = [
		"dank",
		"cursed",
		"pov",
		"nocap",
		"rizz",
		"sigma",
		"goofy-ahh",
		"touchgrass",
		"gyatt",
		"sus",
		"mid",
		"relatable",
		"cringe",
		"shitpost",
		"edgy",
		"deep-fried",
		"npc",
		"dead",
		"skibidi",
		"toilet",
	];
	foreach ($tags as $tag) {
		if (isset($_POST["tag-$tag"])) {
			echo "Tag sélectionné : " . $tag . "<br>";
		}
	}
}
?>
