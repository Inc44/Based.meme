document.addEventListener('DOMContentLoaded', () =>
{
	const passwordInput = document.getElementById('password');
	const passwordError = document.getElementById('password-error');
	passwordInput.addEventListener('input', () =>
	{
		const password = passwordInput.value;
		let message = '';
		if (password === 'password123')
		{
			message = "Really? Is that the best you can do?";
		}
		else if (password.length > 0 && password.length < 8)
		{
			message = "8 characters minimum. Don't be lazy.";
		}
		else if (password.length >= 8 && !/[0-9]/.test(password))
		{
			message = "Add a number. It's Password Security 101.";
		}
		else if (password.length >= 8 && !/[A-Z]/.test(password))
		{
			message = "No uppercase? Are you even trying?";
		}
		else if (password.length > 0 && !/^[\x20-\x7E]+$/.test(password))
		{
			message = "Password must contain only typable ASCII characters.";
		}
		else if (password.length >= 8)
		{
			message = "Meh, I guess that'll do.";
		}
		passwordError.textContent = message;
		passwordError.style.display = message ? 'block' : 'none';
	});
});