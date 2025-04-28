function loginRequired()
{
	const toast = document.createElement('div');
	toast.textContent = 'Imagine wanting premium content without an account, lol!';
	toast.className = 'toast';
	document.body.appendChild(toast);
	setTimeout(() =>
	{
		document.body.removeChild(toast);
	}, 3000);
}