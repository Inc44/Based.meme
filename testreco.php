<?php
require_once "db_connect.php";
$pdo = getDbConnection();
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$username = $_POST["username"];
	$secret_question = $_POST["secret-question"];
	$secret_answer = $_POST["secret-answer"];
	$password = $_POST["password"];
	$key = "super_secure_secret_key_123!";
	$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));
	$encrypted_answer = openssl_encrypt(
		$secret_answer,
		"aes-256-cbc",
		$key,
		0,
		$iv
	);
	$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
	$stmt->execute(["username" => $username]);
	$user = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($user) {
		if ($encrypted_answer === $user["secret_answer"]) {
			echo "<p class='message'>✅ The answer to the secret question is correct! 🎉 You've cracked the code like a true cybersecurity wizard!</p>";
			$new_password_hash = password_hash($password, PASSWORD_DEFAULT);
			$update_stmt = $pdo->prepare(
				"UPDATE users SET password_hash = :password_hash WHERE username = :username"
			);
			$update_stmt->execute([
				"password_hash" => $new_password_hash,
				"username" => $username,
			]);
			echo "<p class='message'>🔒 Your password has been updated successfully! 🛡️ You're now ready to enter the digital kingdom with your brand new password.</p>";
		} else {
			echo "<p class='message'>❌ Oops! The secret question answer is incorrect. 🧠 Maybe try remembering your first pet's name? Try again, you're almost there!</p>";
		}
	} else {
		echo "<p class='message'>❌ No match found for this username. 🤔 Double-check and make sure you typed it correctly. We promise we're not trying to be mysterious… or are we?</p>";
	}
} else {
	echo "<p class='message'>🚨 Please fill out the form below to recover your account. Your digital identity is hanging in the balance! 🕵️♂️</p>";
	echo "<p class='message'>💭 Don't forget your secret question answer... or your account might fade into the abyss of forgotten passwords forever. 🌌</p>";
}
?>
<!-- Formulaire Recup -->
<form method="POST">
	<label for="username">Nom d'utilisateur</label>
	<input type="text" id="username" name="username" required>
	<label for="secret-question">Question secrète</label>
	<select id="secret-question" name="secret-question" required>
		<option value="first-pet">Quel était le nom de ton premier animal de compagnie ?</option>
		<option value="mother-maiden-name">Quel est le nom de jeune fille de ta mère ?</option>
		<option value="favorite-book">Quel est ton livre préféré ?</option>
	</select>
	<label for="secret-answer">Réponse à la question secrète</label>
	<input type="text" id="secret-answer" name="secret-answer" required>
	<label for="password">Nouveau mot de passe</label>
	<input type="password" id="password" name="password" required>
	<button type="submit">Réinitialiser mon mot de passe</button>
</form>