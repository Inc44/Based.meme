document.addEventListener('DOMContentLoaded', () =>
{
	const toggle = document.getElementById('themeToggle');
	const setTheme = (isDark) =>
	{
		document.body.classList.toggle('light-theme', !isDark);
		toggle.textContent = isDark ? '🌙' : '☀️';
	};
	setTheme(localStorage.getItem('theme') !== 'light');
	toggle.onclick = () =>
	{
		const isDark = document.body.classList.contains('light-theme');
		localStorage.setItem('theme', isDark ? 'dark' : 'light');
		setTheme(isDark);
	};
});