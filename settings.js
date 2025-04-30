document.addEventListener('DOMContentLoaded', () =>
{
	const avatarInput = document.getElementById('avatarInput');
	const avatarDrop = document.getElementById('avatarDrop');
	const avatarPreview = document.getElementById('avatarPreview');
	const avatarUrlInput = document.getElementById('avatarUrlInput');
	const avatarMode = document.getElementById('avatarMode');
	const preview = (src) =>
	{
		if (src) avatarPreview.src = src;
	};
	avatarInput.addEventListener('change', () =>
	{
		if (!avatarInput.files.length) return;
		const file = avatarInput.files[0];
		const fileReader = new FileReader();
		fileReader.onload = () => preview(fileReader.result);
		fileReader.readAsDataURL(file);
		avatarMode.value = 'file';
	});
	['dragenter', 'dragover'].forEach((eventName) => avatarDrop.addEventListener(eventName, (event) =>
	{
		event.preventDefault();
		avatarDrop.classList.add('dragging');
	}));
	['dragleave', 'drop'].forEach((eventName) => avatarDrop.addEventListener(eventName, (event) =>
	{
		event.preventDefault();
		avatarDrop.classList.remove('dragging');
	}));
	avatarDrop.addEventListener('drop', (event) =>
	{
		if (!event.dataTransfer.files.length) return;
		avatarInput.files = event.dataTransfer.files;
		const fileReader = new FileReader();
		fileReader.onload = () => preview(fileReader.result);
		fileReader.readAsDataURL(event.dataTransfer.files[0]);
		avatarMode.value = 'file';
	});
	avatarUrlInput.addEventListener('input', () =>
	{
		avatarMode.value = 'url';
		preview(avatarUrlInput.value.trim());
	});
	const bio = document.getElementById('bio');
	const bioCount = document.getElementById('bioCount');
	const update = () =>
	{
		const len = bio.value.length;
		bioCount.textContent = `${len}/69`;
		bioCount.style.color = len > 69 ? 'var(--accent)' : '';
	};
	if (bio)
	{
		update();
		bio.addEventListener('input', update);
	}
	const passwordInput = document.getElementById('password');
	const passwordError = document.getElementById('password-error');
	const confirmPasswordGroup = document.getElementById('confirmPasswordGroup');
	const confirmPasswordInput = document.getElementById('confirm-password');
	if (passwordInput && confirmPasswordGroup && confirmPasswordInput)
	{
		passwordInput.addEventListener('input', () =>
		{
			const hasValue = passwordInput.value.length > 0;
			confirmPasswordGroup.style.display = hasValue ? 'block' : 'none';
			confirmPasswordInput.required = hasValue;
		});
	}
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