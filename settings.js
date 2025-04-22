document.addEventListener('DOMContentLoaded', () =>
{
	const avatarInput = document.getElementById('avatarInput');
	const avatarDrop = document.getElementById('avatarDrop');
	const avatarPreview = document.getElementById('avatarPreview');
	const avatarUrlInput = document.getElementById('avatarUrlInput');
	const avatarMode = document.getElementById('avatarMode');
	const bio = document.getElementById('bio');
	const bioCount = document.getElementById('bioCount');
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
		preview(avatarUrlInput.value);
	});
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
});