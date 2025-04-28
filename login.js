document.addEventListener('DOMContentLoaded', () =>
{
	const loginButton = document.getElementById('loginButton');
	const loginForm = document.getElementById('loginForm');
	if (!loginButton || !loginForm) return;
	let submitTimeoutId = null;
	window.addEventListener('pageshow', (event) =>
	{
		if (event.persisted)
		{
			clearTimeout(submitTimeoutId);
			submitTimeoutId = null;
			loginButton.disabled = false;
			loginButton.classList.remove('animation');
			loginButton.style.width = '';
			loginButton.style.height = '';
			loginButton.style.minWidth = '';
			loginButton.style.minHeight = '';
			loginButton.textContent = loginButton.textContent;
			loginButton.querySelectorAll('.ball')
				.forEach(ball => ball.remove());
		}
	});
	loginButton.addEventListener('click', (event) =>
	{
		event.preventDefault();
		if (!loginForm.checkValidity())
		{
			loginForm.reportValidity();
			return;
		}
		clearTimeout(submitTimeoutId);
		submitTimeoutId = setTimeout(() => loginForm.submit(), 1200);
		loginButton.disabled = true;
		loginButton.classList.add('animation');
		const computedStyle = window.getComputedStyle(loginButton);
		const buttonWidth = computedStyle.width;
		const buttonHeight = computedStyle.height;
		loginButton.style.width = buttonWidth;
		loginButton.style.height = buttonHeight;
		loginButton.style.minWidth = buttonWidth;
		loginButton.style.minHeight = buttonHeight;
		loginButton.textContent = '';
		const balls = [];
		const ballSize = 8;
		const buttonWidthPx = parseFloat(buttonWidth);
		const buttonHeightPx = parseFloat(buttonHeight);
		const rackPositionX = buttonWidthPx / 2 + ballSize * 1.2;
		const rackPositionY = buttonHeightPx / 2;
		const cueBallX = buttonWidthPx / 2 - ballSize * 3;
		const strikeDistance = ballSize * 4.5;
		const spacingX = ballSize + 1;
		const spacingY = ballSize + 1;
		const rowsConfiguration = [1, 2, 3];
		let currentX = rackPositionX;
		const cueBall = document.createElement('div');
		cueBall.className = 'ball';
		cueBall.style.left = `${cueBallX}px`;
		cueBall.style.top = `${rackPositionY}px`;
		loginButton.appendChild(cueBall);
		balls.push(cueBall);
		rowsConfiguration.forEach(ballsInRow =>
		{
			let currentY = rackPositionY - (ballsInRow - 1) * spacingY / 2;
			for (let i = 0; i < ballsInRow; i++)
			{
				const ball = document.createElement('div');
				ball.className = 'ball';
				ball.style.left = `${currentX}px`;
				ball.style.top = `${currentY}px`;
				loginButton.appendChild(ball);
				balls.push(ball);
				currentY += spacingY;
			}
			currentX += spacingX;
		});
		setTimeout(() =>
		{
			cueBall.classList.add('cue-ball-moving');
			cueBall.style.transform = `translate(-50%, -50%) translateX(${strikeDistance}px)`;
			cueBall.addEventListener('transitionend', () =>
			{
				cueBall.classList.remove('cue-ball-moving');
				setTimeout(() =>
				{
					balls.slice(1)
						.forEach(rackBall =>
						{
							const randomX = (Math.random() - 0.5) * buttonWidthPx * 1.4;
							const randomY = (Math.random() - 0.5) * buttonHeightPx * 1.4;
							const randomRotation = (Math.random() - 0.5) * 720;
							rackBall.style.transform = `translate(-50%, -50%) translate(${randomX}px, ${randomY}px) rotate(${randomRotation}deg)`;
						});
				}, 30);
			},
			{
				once: true
			});
		}, 100);
	});
});