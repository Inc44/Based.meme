document.addEventListener('DOMContentLoaded', () =>
{
	const bioToggle = document.querySelector('.bio-toggle');
	const bioText = document.querySelector('.bio-text');
	if (bioToggle && bioText)
	{
		bioToggle.addEventListener('click', () =>
		{
			const fullBio = bioText.dataset.full;
			if (bioText.textContent === fullBio)
			{
				bioText.textContent = fullBio.slice(0, 69) + '...';
				bioToggle.textContent = 'Show more';
			}
			else
			{
				bioText.textContent = fullBio;
				bioToggle.textContent = 'Show less';
			}
		});
	}
});