document.addEventListener('DOMContentLoaded', () =>
{
	const memeInput = document.getElementById('upload-image-fichier');
	const memeDrop = document.getElementById('dropZone');
	const memePreview = document.getElementById('preview');
	const memeUrlInput = document.getElementById('memeUrlInput');
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
	const handleFile = (file) =>
	{
		const fileReader = new FileReader();
		fileReader.onload = () => preview(fileReader.result);
		fileReader.readAsDataURL(file);
		memeUrlInput.value = '';
	};
	memeInput.addEventListener('change', () =>
	{
		if (memeInput.files.length) handleFile(memeInput.files[0]);
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
		if (event.dataTransfer.files.length)
		{
			memeInput.files = event.dataTransfer.files;
			handleFile(event.dataTransfer.files[0]);
		}
	});
	document.addEventListener('paste', (event) =>
	{
		if (event.clipboardData.files.length)
		{
			memeInput.files = event.clipboardData.files;
			handleFile(event.clipboardData.files[0]);
		}
		else
		{
			const url = event.clipboardData.getData('text')
				.trim();
			if (url && (url.startsWith('http://') || url.startsWith('https://')))
			{
				preview(url);
				memeInput.value = '';
				memeUrlInput.value = url;
			}
		}
	});
	memeUrlInput.addEventListener('input', () =>
	{
		const url = memeUrlInput.value.trim();
		if (url)
		{
			preview(url);
			memeInput.value = '';
		}
	});
});