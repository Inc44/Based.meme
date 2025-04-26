function copyLink()
{
	const url = location.href;
	if (navigator.clipboard)
	{
		navigator.clipboard.writeText(url);
		const toast = document.createElement('div');
		toast.textContent = 'Link copied!';
		toast.className = 'toast';
		document.body.appendChild(toast);
		setTimeout(() =>
		{
			document.body.removeChild(toast);
		}, 3000);
	}
}

function toggleReply(id)
{
	const form = document.getElementById(id);
	form.classList.toggle('hidden');
	if (!form.classList.contains('hidden'))
	{
		form.querySelector('textarea')
			.focus();
	}
}
document.addEventListener('DOMContentLoaded', () =>
{
	const textareas = document.querySelectorAll('.comment-form textarea, .reply-form textarea');
	textareas.forEach(textarea =>
	{
		textarea.addEventListener('keydown', event =>
		{
			if (event.key === 'Enter' && !event.shiftKey)
			{
				event.preventDefault();
				textarea.closest('form')
					.submit();
			}
		});
	});
});