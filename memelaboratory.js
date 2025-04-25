document.addEventListener('DOMContentLoaded', () =>
{
	const memeInput = document.getElementById('upload-image-fichier');
	const memeDrop = document.getElementById('dropZone');
	const memePreview = document.getElementById('preview');
	const memeUrlInput = document.getElementById('memeUrlInput');
	const memeMode = document.getElementById('memeMode');
	const placeholder = memeDrop?.querySelector('.placeholder-image-desc');
	const overlay = memeDrop?.querySelector('.locked-overlay');
	const preview = (src) =>
	{
		if (!src) return;
		memePreview.src = src;
		memePreview.style.display = 'block';
		placeholder.style.display = 'none';
		overlay.style.display = 'none';
	};
	memeInput.addEventListener('change', () =>
	{
		if (!memeInput.files.length) return;
		const file = memeInput.files[0];
		const fileReader = new FileReader();
		fileReader.onload = () => preview(fileReader.result);
		fileReader.readAsDataURL(file);
		memeMode.value = 'file';
	});
	['dragenter', 'dragover'].forEach((eventName) => memeDrop.addEventListener(eventName, (event) =>
	{
		event.preventDefault();
		memeDrop.classList.add('dragging');
	}));
	['dragleave', 'drop'].forEach((eventName) => memeDrop.addEventListener(eventName, (event) =>
	{
		event.preventDefault();
		memeDrop.classList.remove('dragging');
	}));
	memeDrop.addEventListener('drop', (event) =>
	{
		if (!event.dataTransfer.files.length) return;
		memeInput.files = event.dataTransfer.files;
		const fileReader = new FileReader();
		fileReader.onload = () => preview(fileReader.result);
		fileReader.readAsDataURL(event.dataTransfer.files[0]);
		memeMode.value = 'file';
	});
	document.addEventListener('paste', (event) =>
	{
		if (event.clipboardData?.files.length)
		{
			memeInput.files = event.clipboardData.files;
			const fileReader = new FileReader();
			fileReader.onload = () => preview(fileReader.result);
			fileReader.readAsDataURL(event.clipboardData.files[0]);
			memeMode.value = 'file';
		}
	});
	memeUrlInput.addEventListener('input', () =>
	{
		memeMode.value = 'url';
		preview(memeUrlInput.value.trim());
	});
});