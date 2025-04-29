document.addEventListener('DOMContentLoaded', () =>
{
	const typeRadios = document.querySelectorAll('input[name="type"]');
	const tagContainer = document.querySelector('.meme-tag-container');
	const updateTagVisibility = () =>
	{
		if (!tagContainer) return;
		const selectedType = document.querySelector('input[name="type"]:checked')
			?.value || 'memes';
		const shouldHide = ['memers', 'tags'].includes(selectedType);
		tagContainer.style.display = shouldHide ? 'none' : 'block';
	};
	typeRadios.forEach(radio =>
	{
		radio.addEventListener('change', updateTagVisibility);
	});
	updateTagVisibility();
});