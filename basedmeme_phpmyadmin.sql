CREATE DATABASE basedmeme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE basedmeme;
SET
	SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET
	time_zone = "+00:00";
CREATE TABLE comments(
	comment_id CHAR(11) NOT NULL,
	meme_id CHAR(11) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	content TEXT NOT NULL,
	parent_id CHAR(11) DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	like_count INT(10) UNSIGNED DEFAULT 0,
	dislike_count INT(10) UNSIGNED DEFAULT 0,
	upvote_count INT(10) UNSIGNED DEFAULT 0,
	is_pinned TINYINT(1) DEFAULT 0
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	comments(
		comment_id,
		meme_id,
		user_id,
		content,
		parent_id,
		created_at,
		updated_at,
		like_count,
		dislike_count,
		upvote_count,
		is_pinned
	)
VALUES
	(
		'_edzxRf4pMk',
		'abcdef12345',
		4,
		'crazy ahhhhh meme',
		NULL,
		'2025-04-28 10:05:33',
		'2025-04-28 10:05:33',
		0,
		0,
		0,
		0
	),
	(
		'_K9iFnjQWQv',
		'r5MR7_INQwg',
		3,
		'AHAHAH 🔥🔥',
		NULL,
		'2025-04-27 10:55:57',
		'2025-04-27 10:55:57',
		0,
		0,
		0,
		0
	),
	(
		'33RT5jvY6sX',
		'aE7sLpbfMHu',
		4,
		'This boy too sexy',
		NULL,
		'2025-04-27 19:33:09',
		'2025-04-27 19:33:09',
		0,
		0,
		0,
		0
	),
	(
		'6smk95iJkXV',
		'tonZT7XUR7S',
		3,
		'😂😂',
		NULL,
		'2025-04-27 16:29:35',
		'2025-04-27 16:29:35',
		0,
		0,
		0,
		0
	),
	(
		'bXpXJwM_c0H',
		'9hZNbQEuyu5',
		4,
		'C\'est quoi ce meme de neuille encore',
		NULL,
		'2025-04-27 19:38:49',
		'2025-04-27 19:38:49',
		0,
		0,
		0,
		0
	),
	(
		'h-GpRhAaro-',
		'8AHrSSNJoWz',
		2,
		'Human design',
		NULL,
		'2025-04-27 12:59:04',
		'2025-04-27 12:59:04',
		0,
		0,
		0,
		0
	),
	(
		'NzAm4BvCDkS',
		'tonZT7XUR7S',
		4,
		'Crazy old guy anyway',
		NULL,
		'2025-04-27 19:39:40',
		'2025-04-27 19:39:40',
		0,
		0,
		0,
		0
	),
	(
		'WLunsc47oVG',
		'yXAFlHppvWz',
		3,
		'💀💀💀',
		NULL,
		'2025-04-27 20:15:55',
		'2025-04-27 20:15:55',
		0,
		0,
		0,
		0
	),
	(
		'YCFN-O9tidM',
		'aE7sLpbfMHu',
		3,
		'HELL NAAHH',
		'33RT5jvY6sX',
		'2025-04-28 11:02:42',
		'2025-04-28 11:02:42',
		0,
		0,
		0,
		0
	),
	(
		'YS2MDZHUeZR',
		'nbqMIBYJlvk',
		3,
		'liquid trees 🗣️🗣️🔥🔥',
		NULL,
		'2025-04-27 10:59:30',
		'2025-04-27 10:59:30',
		0,
		0,
		0,
		0
	);
CREATE TABLE follows(
	follower_id BIGINT(20) UNSIGNED NOT NULL,
	following_id BIGINT(20) UNSIGNED NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	follows(follower_id, following_id, created_at)
VALUES
	(1, 3, '2025-05-01 00:00:00'),
	(2, 1, '2025-05-01 00:00:00'),
	(2, 3, '2025-05-01 00:00:00');
CREATE TABLE memes(
	meme_id CHAR(11) NOT NULL,
	slug VARCHAR(256) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	title VARCHAR(512) NOT NULL,
	content TEXT DEFAULT NULL,
	media_url TEXT DEFAULT NULL,
	status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
	visibility ENUM('public', 'private', 'followers') DEFAULT 'public',
	spiciness DECIMAL(3, 2) UNSIGNED NOT NULL DEFAULT 0.00,
	published_at TIMESTAMP NULL DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	view_count INT(10) UNSIGNED DEFAULT 0,
	like_count INT(10) UNSIGNED DEFAULT 0,
	dislike_count INT(10) UNSIGNED DEFAULT 0,
	upvote_count INT(10) UNSIGNED DEFAULT 0,
	comment_count INT(10) UNSIGNED DEFAULT 0,
	share_count INT(10) UNSIGNED DEFAULT 0,
	saved_count INT(10) UNSIGNED DEFAULT 0,
	allow_comments TINYINT(1) DEFAULT 1
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	memes(
		meme_id,
		slug,
		user_id,
		title,
		content,
		media_url,
		status,
		visibility,
		spiciness,
		published_at,
		created_at,
		updated_at,
		view_count,
		like_count,
		dislike_count,
		upvote_count,
		comment_count,
		share_count,
		saved_count,
		allow_comments
	)
VALUES
	(
		'0hQt_Y6K0o8',
		'-0hQt_Y6K0o8',
		2,
		'Well well well',
		'',
		'https://images7.memedroid.com/images/UPLOADED943/680e2056b3648.webp',
		'published',
		'public',
		0.50,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'8AHrSSNJoWz',
		'-8AHrSSNJoWz',
		3,
		'no words needed...',
		'',
		'https://s3.thehackerblog.com/findthatmeme/3b4a7779-0498-48ba-933e-2adc92641432.jpeg',
		'published',
		'public',
		0.68,
		'2025-04-27 11:46:27',
		'2025-04-27 11:46:27',
		'2025-04-27 12:59:04',
		0,
		1,
		0,
		1,
		1,
		0,
		1,
		1
	),
	(
		'9hZNbQEuyu5',
		'-ooking-at-something-better-or-so-you-think--9hZNbQEuyu5',
		3,
		'Relationship Choices',
		'Looking at something better... or so you think.',
		'https://upload.wikimedia.org/wikipedia/en/b/be/Disloyal_man_with_his_girlfriend_looking_at_another_girl.jpg',
		'published',
		'public',
		0.00,
		'2025-04-27 11:26:32',
		'2025-04-27 11:26:32',
		'2025-04-27 19:38:55',
		0,
		1,
		0,
		0,
		1,
		0,
		1,
		1
	),
	(
		'a-VQFjsH91k',
		'-a-VQFjsH91k',
		3,
		'indognito mode',
		'',
		'https://i.chzbgr.com/full/9340538368/hEBD499F8/canidae-when-you-dont-want-be-recognized-so-you-go-indognito-sneak-100-illusion-100',
		'published',
		'public',
		0.82,
		'2025-04-29 16:54:30',
		'2025-04-29 16:54:30',
		'2025-04-29 16:54:32',
		0,
		1,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'abcdef12345',
		'lorem-picsum',
		1,
		'Lorem Picsum',
		'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		'https://picsum.photos/400',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		1,
		1,
		1,
		0,
		0,
		1
	),
	(
		'Ae_X5dRGbq2',
		'-Ae_X5dRGbq2',
		2,
		'Working with others using GitHub',
		'',
		'https://i.redd.it/ll7f77rl7d871.jpg',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		2,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'aE7sLpbfMHu',
		'-hen-you-know-you-are-too-sexy--aE7sLpbfMHu',
		4,
		'Neuille',
		'When you know you are too sexy ..',
		'https://i.ibb.co/C5hMRsSr/WIN-20241209-10-25-11-Pro.jpg',
		'published',
		'public',
		1.00,
		'2025-04-27 19:32:41',
		'2025-04-27 19:32:41',
		'2025-04-28 11:33:11',
		0,
		2,
		0,
		0,
		2,
		0,
		0,
		1
	),
	(
		'CuKWeN-z4Yj',
		'-aughing-instead-of-admitting-you-have-no-idea-what-they-said--CuKWeN-z4Yj',
		3,
		'Socially Awkward Survival',
		'Laughing instead of admitting you have no idea what they said.',
		'https://cdn.memes.com/up/71558571535638926/i/1744821931573.jpg',
		'published',
		'public',
		0.57,
		'2025-04-27 11:30:46',
		'2025-04-27 11:30:46',
		'2025-04-28 10:06:26',
		0,
		1,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'HbnBSf14UQN',
		'-HbnBSf14UQN',
		3,
		'Larry..',
		'',
		'https://pbs.twimg.com/media/Giu_7UaWcAMsBZz?format=jpg&name=large',
		'published',
		'public',
		1.00,
		'2025-04-29 17:00:37',
		'2025-04-29 17:00:37',
		'2025-04-29 17:00:37',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'iVoGZ6hhUfi',
		'-iVoGZ6hhUfi',
		2,
		'Tips fedora with rizz',
		'',
		'uploads/mFX-xZDy3gj.png',
		'published',
		'public',
		1.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		1,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'lEZJuPSiVis',
		'-lEZJuPSiVis',
		2,
		'Pain of senior devs',
		'',
		'https://preview.redd.it/ln6le1ksmso71.jpg?auto=webp&s=0bac42a19e22cfe162e812e29a2d508c4b2ce814',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'MBJFPq2Llps',
		'mass-insanity',
		1,
		'Mass Insanity',
		'Current situation in the USA',
		'https://images7.memedroid.com/images/UPLOADED250/68030d08f108d.jpeg',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'nbqMIBYJlvk',
		'liquid-trees',
		1,
		'Liquid Trees',
		'What\'s wrong with trees?',
		'https://images7.memedroid.com/images/UPLOADED915/6802ea339e30b.jpeg',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		0,
		0,
		1,
		0,
		0,
		1
	),
	(
		'qdG5QxoF-04',
		'-qdG5QxoF-04',
		2,
		'Merging commits is like',
		'',
		'https://preview.redd.it/s3mm1b2w6m041.png?auto=webp&s=cd2ff57d75640e05e850a1eeeb482df7a7c7ddb7',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		1,
		1,
		0,
		0,
		0,
		1,
		1
	),
	(
		'r5MR7_INQwg',
		'republic-vs-monarchy',
		1,
		'Republic vs Monarchy',
		'A Satirical Take on Government Systems',
		'https://images3.memedroid.com/images/UPLOADED587/5fea88b9716eb.jpeg',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		1,
		0,
		0,
		1,
		0,
		0,
		1
	),
	(
		'tonZT7XUR7S',
		'-tonZT7XUR7S',
		2,
		'Being a senior dev be like',
		'',
		'https://i.programmerhumor.io/2025/03/46d1ba366c795b2a360b87b87025bbb1.png',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		1,
		2,
		0,
		1,
		2,
		0,
		0,
		1
	),
	(
		'XCZJOh8M56T',
		'-XCZJOh8M56T',
		3,
		'Linux..',
		'',
		'https://s3.thehackerblog.com/findthatmeme/11c6a648-8bd5-4b03-b010-3c857ff988c1.png',
		'published',
		'public',
		0.69,
		'2025-04-29 19:22:35',
		'2025-04-29 19:22:35',
		'2025-04-29 19:22:35',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'xhRGlIGKuJB',
		'-xhRGlIGKuJB',
		3,
		'Stupid Command Block',
		'',
		'https://s3.thehackerblog.com/findthatmeme/ed27157e-95c3-48a4-8c69-3456e164cb7e.jpg',
		'published',
		'public',
		0.52,
		'2025-04-27 11:42:09',
		'2025-04-27 11:42:09',
		'2025-04-28 12:58:15',
		0,
		0,
		0,
		0,
		2,
		0,
		0,
		1
	),
	(
		'yXAFlHppvWz',
		'-yXAFlHppvWz',
		2,
		'Proper way to commit after merge conflict',
		'',
		'https://i.programmerhumor.io/2025/03/155f93e10ab8ccc2345f65e5005ec1daea2387959794bedc687675fd4a661896.jpeg',
		'published',
		'public',
		0.00,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		0,
		0,
		0,
		0,
		1,
		0,
		0,
		1
	),
	(
		'z3_j8DkJVz8',
		'test-z3_j8DkJVz8',
		4,
		'test',
		'test',
		'https://cdn.futura-sciences.com/cdn-cgi/image/width=1920,quality=50,format=auto/sources/images/dossier/773/01-intro-773.jpg',
		'published',
		'public',
		0.00,
		'2025-04-27 21:36:22',
		'2025-04-27 21:36:22',
		'2025-04-27 23:49:40',
		0,
		0,
		0,
		0,
		0,
		0,
		0,
		1
	),
	(
		'I3w3V-9xoqy',
		'-e-roi-des-neuilles-ing-of-the-neuilles-I3w3V-9xoqy',
		4,
		'Suprema Neuille',
		'Le roi des neuilles / King of the neuilles',
		'uploads/NvCtXx7Qn7a.jpg',
		'published',
		'public',
		0.17,
		'2025-04-28 00:42:34',
		'2025-04-28 00:42:34',
		'2025-04-28 00:43:08',
		0,
		1,
		0,
		0,
		0,
		0,
		0,
		1
	);
CREATE TABLE meme_subs(
	meme_id CHAR(11) NOT NULL,
	sub_id BIGINT(20) UNSIGNED NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
CREATE TABLE meme_tags(
	meme_id CHAR(11) NOT NULL,
	tag_id BIGINT(20) UNSIGNED NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	meme_tags(meme_id, tag_id, created_at)
VALUES
	(
		'0hQt_Y6K0o8',
		18,
		'2025-05-01 00:00:00'
	),
	(
		'8AHrSSNJoWz',
		20,
		'2025-05-01 00:00:00'
	),
	('9hZNbQEuyu5', 9, '2025-05-01 00:00:00'),
	(
		'9hZNbQEuyu5',
		17,
		'2025-05-01 00:00:00'
	),
	(
		'9hZNbQEuyu5',
		18,
		'2025-05-01 00:00:00'
	),
	('a-VQFjsH91k', 7, '2025-05-01 00:00:00'),
	(
		'a-VQFjsH91k',
		14,
		'2025-05-01 00:00:00'
	),
	(
		'abcdef12345',
		10,
		'2025-05-01 00:00:00'
	),
	('Ae_X5dRGbq2', 3, '2025-05-01 00:00:00'),
	('Ae_X5dRGbq2', 8, '2025-05-01 00:00:00'),
	(
		'Ae_X5dRGbq2',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'Ae_X5dRGbq2',
		18,
		'2025-05-01 00:00:00'
	),
	('aE7sLpbfMHu', 5, '2025-05-01 00:00:00'),
	('aE7sLpbfMHu', 6, '2025-05-01 00:00:00'),
	('aE7sLpbfMHu', 7, '2025-05-01 00:00:00'),
	('aE7sLpbfMHu', 9, '2025-05-01 00:00:00'),
	('CuKWeN-z4Yj', 4, '2025-05-01 00:00:00'),
	(
		'CuKWeN-z4Yj',
		12,
		'2025-05-01 00:00:00'
	),
	('I3w3V-9xoqy', 6, '2025-05-01 00:00:00'),
	('I3w3V-9xoqy', 7, '2025-05-01 00:00:00'),
	(
		'I3w3V-9xoqy',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'I3w3V-9xoqy',
		18,
		'2025-05-01 00:00:00'
	),
	(
		'I3w3V-9xoqy',
		19,
		'2025-05-01 00:00:00'
	),
	(
		'I3w3V-9xoqy',
		20,
		'2025-05-01 00:00:00'
	),
	('HbnBSf14UQN', 7, '2025-05-01 00:00:00'),
	(
		'HbnBSf14UQN',
		14,
		'2025-05-01 00:00:00'
	),
	('iVoGZ6hhUfi', 6, '2025-05-01 00:00:00'),
	(
		'iVoGZ6hhUfi',
		10,
		'2025-05-01 00:00:00'
	),
	('lEZJuPSiVis', 3, '2025-05-01 00:00:00'),
	('lEZJuPSiVis', 8, '2025-05-01 00:00:00'),
	(
		'lEZJuPSiVis',
		10,
		'2025-05-01 00:00:00'
	),
	(
		'lEZJuPSiVis',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'lEZJuPSiVis',
		18,
		'2025-05-01 00:00:00'
	),
	('MBJFPq2Llps', 2, '2025-05-01 00:00:00'),
	(
		'MBJFPq2Llps',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'MBJFPq2Llps',
		14,
		'2025-05-01 00:00:00'
	),
	(
		'MBJFPq2Llps',
		15,
		'2025-05-01 00:00:00'
	),
	('nbqMIBYJlvk', 7, '2025-05-01 00:00:00'),
	(
		'nbqMIBYJlvk',
		11,
		'2025-05-01 00:00:00'
	),
	(
		'nbqMIBYJlvk',
		14,
		'2025-05-01 00:00:00'
	),
	('qdG5QxoF-04', 3, '2025-05-01 00:00:00'),
	(
		'qdG5QxoF-04',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'r5MR7_INQwg',
		11,
		'2025-05-01 00:00:00'
	),
	(
		'r5MR7_INQwg',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'r5MR7_INQwg',
		14,
		'2025-05-01 00:00:00'
	),
	('tonZT7XUR7S', 3, '2025-05-01 00:00:00'),
	(
		'tonZT7XUR7S',
		12,
		'2025-05-01 00:00:00'
	),
	('XCZJOh8M56T', 4, '2025-05-01 00:00:00'),
	(
		'XCZJOh8M56T',
		12,
		'2025-05-01 00:00:00'
	),
	('xhRGlIGKuJB', 8, '2025-05-01 00:00:00'),
	(
		'xhRGlIGKuJB',
		12,
		'2025-05-01 00:00:00'
	),
	('yXAFlHppvWz', 3, '2025-05-01 00:00:00'),
	('yXAFlHppvWz', 8, '2025-05-01 00:00:00'),
	(
		'yXAFlHppvWz',
		12,
		'2025-05-01 00:00:00'
	),
	(
		'yXAFlHppvWz',
		18,
		'2025-05-01 00:00:00'
	);
CREATE TABLE notifications(
	notification_id CHAR(11) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	type ENUM(
		'like',
		'comment',
		'follow',
		'mention',
		'system'
	) NOT NULL,
	content VARCHAR(512) NOT NULL,
	reference_id CHAR(11) DEFAULT NULL,
	is_read TINYINT(1) DEFAULT 0,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
CREATE TABLE privacy_consents(
	consent_id CHAR(11) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	consent_type ENUM(
		'terms',
		'privacy',
		'cookies',
		'marketing',
		'third_party'
	) NOT NULL,
	is_granted TINYINT(1) NOT NULL DEFAULT 1,
	ip_address VARCHAR(45) DEFAULT NULL,
	user_agent VARCHAR(512) DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	privacy_consents(
		consent_id,
		user_id,
		consent_type,
		is_granted,
		ip_address,
		user_agent,
		created_at,
		updated_at
	)
VALUES
	(
		'4_45w2g3r59',
		1,
		'privacy',
		1,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'4I-TdNeXpSG',
		1,
		'terms',
		1,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'g47c1F_aL0y',
		1,
		'third_party',
		1,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'qCUCBQdnwib',
		1,
		'marketing',
		1,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'uoz7QaeM9UZ',
		1,
		'cookies',
		1,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	);
CREATE TABLE reports(
	report_id CHAR(11) NOT NULL,
	reporter_id BIGINT(20) UNSIGNED NOT NULL,
	content_type ENUM('meme', 'comment', 'user') NOT NULL,
	content_id CHAR(11) NOT NULL,
	reason ENUM(
		'minor',
		'misinformation',
		'spam',
		'bug',
		'report',
		'suggestion',
		'collab',
		'other'
	) NOT NULL,
	details TEXT DEFAULT NULL,
	resolved_by BIGINT(20) UNSIGNED DEFAULT NULL,
	status ENUM(
		'pending',
		'reviewing',
		'resolved',
		'rejected'
	) DEFAULT 'pending',
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	resolved_at TIMESTAMP NULL DEFAULT NULL
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	reports(
		report_id,
		reporter_id,
		content_type,
		content_id,
		reason,
		details,
		resolved_by,
		status,
		created_at,
		updated_at,
		resolved_at
	)
VALUES
	(
		'--LBB_oDGHm',
		4,
		'user',
		'qx2bkEG9qmD',
		'suggestion',
		'Submitted via contact form by: TheSexyChocolat (redactedforprivacy@hotmail.com)\n\nJ\'ai une idée de couche culotte qui recycle le caca',
		NULL,
		'pending',
		'2025-04-27 19:37:27',
		'2025-04-27 19:37:27',
		NULL
	);
CREATE TABLE subs(
	sub_id BIGINT(20) UNSIGNED NOT NULL,
	name VARCHAR(1024) NOT NULL,
	slug VARCHAR(256) NOT NULL,
	description TEXT DEFAULT NULL,
	icon VARCHAR(260) DEFAULT NULL,
	user_count INT(10) UNSIGNED DEFAULT 0,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
CREATE TABLE tags(
	tag_id BIGINT(20) UNSIGNED NOT NULL,
	name VARCHAR(1024) NOT NULL,
	slug VARCHAR(256) NOT NULL,
	usage_count INT(10) UNSIGNED DEFAULT 0,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	tags(
		tag_id,
		name,
		slug,
		usage_count,
		created_at,
		updated_at
	)
VALUES
	(
		1,
		'dank',
		'dank',
		0,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'cursed',
		'cursed',
		1,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		3,
		'pov',
		'pov',
		5,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'no cap fr fr',
		'no-cap-fr-fr',
		3,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		5,
		'riz z',
		'rizz',
		4,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		6,
		'sigma',
		'sigma',
		6,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		7,
		'goofy-ahh',
		'goofy-ahh',
		10,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		8,
		'touch grass',
		'touch-grass',
		4,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		9,
		'gyatt',
		'gyatt',
		3,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		10,
		'sus',
		'sus',
		3,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		11,
		'mid',
		'mid',
		2,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		12,
		'relatable',
		'relatable',
		12,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		13,
		'cringe',
		'cringe',
		0,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		14,
		'shitpost',
		'shitpost',
		6,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		15,
		'adgy',
		'adgy',
		1,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		16,
		'deep-fried',
		'deep-fried',
		1,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		17,
		'npc',
		'npc',
		1,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		18,
		'💀',
		'skull',
		9,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		19,
		'skidibi',
		'skibidi',
		4,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		20,
		'toilet',
		'toilet',
		3,
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	);
CREATE TABLE uploads(
	upload_id CHAR(11) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	file_name VARCHAR(260) NOT NULL,
	file_path VARCHAR(260) NOT NULL,
	file_type VARCHAR(32) NOT NULL,
	file_size INT(10) UNSIGNED NOT NULL,
	meme_id CHAR(11) DEFAULT NULL,
	width INT(10) UNSIGNED DEFAULT NULL,
	height INT(10) UNSIGNED DEFAULT NULL,
	duration INT(10) UNSIGNED DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	uploads(
		upload_id,
		user_id,
		file_name,
		file_path,
		file_type,
		file_size,
		meme_id,
		width,
		height,
		duration,
		created_at
	)
VALUES
	(
		'mFX-xZDy3gj',
		2,
		'31.png',
		'uploads/mFX-xZDy3gj.png',
		'image/png',
		402723,
		'iVoGZ6hhUfi',
		NULL,
		NULL,
		NULL,
		'2025-05-01 00:00:00'
	),
	(
		'NvCtXx7Qn7a',
		4,
		'WIN_20241108_20_16_31_Pro.jpg',
		'uploads/NvCtXx7Qn7a.jpg',
		'image/jpeg',
		74359,
		'I3w3V-9xoqy',
		NULL,
		NULL,
		NULL,
		'2025-04-28 00:42:34'
	);
CREATE TABLE users(
	user_id BIGINT(20) UNSIGNED NOT NULL,
	handle VARCHAR(32) NOT NULL,
	email VARCHAR(254) NOT NULL,
	password_hash VARCHAR(64) NOT NULL,
	display_name VARCHAR(1024) DEFAULT NULL,
	bio TEXT DEFAULT NULL,
	avatar VARCHAR(260) DEFAULT NULL,
	birthday DATE DEFAULT NULL,
	sex VARCHAR(32) DEFAULT NULL,
	orientation VARCHAR(32) DEFAULT NULL,
	pronouns VARCHAR(32) DEFAULT NULL,
	touch_grass VARCHAR(32) DEFAULT NULL,
	meme_knowledge TEXT DEFAULT NULL,
	secret_question VARCHAR(255) NOT NULL,
	secret_answer_hash VARCHAR(64) NOT NULL,
	location VARCHAR(128) DEFAULT NULL,
	joined_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
	last_login TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	is_verified TINYINT(1) DEFAULT 0,
	is_private TINYINT(1) DEFAULT 0,
	is_admin TINYINT(1) DEFAULT 0,
	is_banned TINYINT(1) DEFAULT 0,
	is_active TINYINT(1) DEFAULT 1
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	users(
		user_id,
		handle,
		email,
		password_hash,
		display_name,
		bio,
		avatar,
		birthday,
		sex,
		orientation,
		pronouns,
		touch_grass,
		meme_knowledge,
		secret_question,
		secret_answer_hash,
		location,
		joined_at,
		updated_at,
		last_login,
		is_verified,
		is_private,
		is_admin,
		is_banned,
		is_active
	)
VALUES
	(
		1,
		'test',
		'redactedforprivacy@example.com',
		'$2y$10$',
		'Test User',
		'Test bio',
		NULL,
		'1970-01-01',
		'male',
		'straight',
		'he-him',
		'today',
		'test',
		'first-pet',
		'$2y$10$',
		'',
		'0000-00-00 00:00:00',
		'0000-00-00 00:00:00',
		'0000-00-00 00:00:00',
		1,
		0,
		0,
		0,
		1
	),
	(
		2,
		'Inc44',
		'redactedforprivacy@outlook.com',
		'$2y$10$',
		'Inc44',
		'Melancholic-alcoholic',
		'https://avatars.githubusercontent.com/u/121856799',
		'1970-01-01',
		'no',
		'attack',
		'burger',
		'minecraft',
		'the opposite of cringe',
		'birth-city',
		'$2y$10$',
		NULL,
		'2025-02-26 00:00:00',
		'2025-02-26 00:00:00',
		'2025-05-01 00:00:00',
		1,
		0,
		1,
		0,
		1
	),
	(
		3,
		'Ethan',
		'redactedforprivacy@gmail.com',
		'$2y$10$',
		NULL,
		NULL,
		NULL,
		'1970-01-01',
		'error',
		'straight',
		'he-him',
		'today',
		NULL,
		'first-pet',
		'$2y$10$',
		NULL,
		'2025-04-27 10:42:35',
		'2025-04-30 17:01:51',
		'2025-05-01 00:00:00',
		1,
		0,
		1,
		0,
		1
	),
	(
		4,
		'TheSexyChocolat',
		'redactedforprivacy@hotmail.com',
		'$2y$10$',
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		NULL,
		'potato',
		'$2y$10$',
		NULL,
		'2025-04-27 19:24:27',
		'2025-04-30 17:01:53',
		'2025-05-01 00:00:00',
		1,
		0,
		1,
		0,
		1
	);
CREATE TABLE user_interactions(
	user_id BIGINT(20) UNSIGNED NOT NULL,
	interaction_type ENUM(
		'view',
		'like',
		'dislike',
		'upvote',
		'save',
		'share'
	) NOT NULL,
	content_type ENUM('meme', 'comment') NOT NULL,
	content_id CHAR(11) NOT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	user_interactions(
		user_id,
		interaction_type,
		content_type,
		content_id,
		created_at
	)
VALUES
	(
		2,
		'view',
		'meme',
		'tonZT7XUR7S',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'like',
		'meme',
		'8AHrSSNJoWz',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'like',
		'meme',
		'qdG5QxoF-04',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'dislike',
		'meme',
		'qdG5QxoF-04',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'upvote',
		'meme',
		'8AHrSSNJoWz',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'save',
		'meme',
		'8AHrSSNJoWz',
		'2025-05-01 00:00:00'
	),
	(
		2,
		'save',
		'meme',
		'qdG5QxoF-04',
		'2025-05-01 00:00:00'
	),
	(
		3,
		'like',
		'meme',
		'9hZNbQEuyu5',
		'2025-05-01 00:00:00'
	),
	(
		3,
		'like',
		'meme',
		'Ae_X5dRGbq2',
		'2025-05-01 00:00:00'
	),
	(
		3,
		'like',
		'meme',
		'r5MR7_INQwg',
		'2025-05-01 00:00:00'
	),
	(
		3,
		'like',
		'meme',
		'tonZT7XUR7S',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'like',
		'meme',
		'Ae_X5dRGbq2',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'like',
		'meme',
		'aE7sLpbfMHu',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'like',
		'meme',
		'fiOldnT7bwc',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'like',
		'meme',
		'I3w3V-9xoqy',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'upvote',
		'meme',
		'tonZT7XUR7S',
		'2025-05-01 00:00:00'
	),
	(
		4,
		'save',
		'meme',
		'9hZNbQEuyu5',
		'2025-05-01 00:00:00'
	);
CREATE TABLE user_sessions(
	session_id CHAR(11) NOT NULL,
	user_id BIGINT(20) UNSIGNED NOT NULL,
	ip_address VARCHAR(45) NOT NULL,
	user_agent TEXT DEFAULT NULL,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
	expires_at TIMESTAMP NOT NULL DEFAULT(CURRENT_TIMESTAMP() + INTERVAL 30 DAY),
	last_activity TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
INSERT INTO
	user_sessions(
		session_id,
		user_id,
		ip_address,
		user_agent,
		created_at,
		expires_at,
		last_activity
	)
VALUES
	(
		'004dc5da07a',
		2,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'1181ea84c4d',
		3,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'3f07ddc5faf',
		4,
		'::1',
		'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	),
	(
		'c584a4e7b4f',
		1,
		'::1',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 Edg/135.0.0.0',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00',
		'2025-05-01 00:00:00'
	);
CREATE TABLE user_subs(
	user_id BIGINT(20) UNSIGNED NOT NULL,
	sub_id BIGINT(20) UNSIGNED NOT NULL,
	is_moderator TINYINT(1) DEFAULT 0,
	created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP()
) ENGINE = INNODB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
ALTER TABLE
	comments
ADD
	PRIMARY KEY(comment_id),
ADD
	KEY idx_comment_meme(meme_id),
ADD
	KEY idx_comment_user(user_id),
ADD
	KEY idx_comment_parent(parent_id),
ADD
	KEY idx_comment_popularity(like_count, upvote_count);
ALTER TABLE
	follows
ADD
	PRIMARY KEY(follower_id, following_id),
ADD
	KEY idx_follow_follower(follower_id),
ADD
	KEY idx_follow_following(following_id);
ALTER TABLE
	memes
ADD
	PRIMARY KEY(meme_id),
ADD
	UNIQUE KEY slug(slug),
ADD
	KEY idx_meme_slug(slug(191)),
ADD
	KEY idx_meme_user(user_id),
ADD
	KEY idx_meme_status(status),
ADD
	KEY idx_meme_visibility(visibility),
ADD
	KEY idx_meme_published_at(published_at),
ADD
	KEY idx_meme_popularity(view_count, like_count, comment_count);
ALTER TABLE
	meme_subs
ADD
	PRIMARY KEY(meme_id, sub_id),
ADD
	KEY idx_meme_subs_sub(sub_id);
ALTER TABLE
	meme_tags
ADD
	PRIMARY KEY(meme_id, tag_id),
ADD
	KEY idx_meme_tags_tag(tag_id);
ALTER TABLE
	notifications
ADD
	PRIMARY KEY(notification_id),
ADD
	KEY idx_notification_user(user_id),
ADD
	KEY idx_notification_read(is_read);
ALTER TABLE
	privacy_consents
ADD
	PRIMARY KEY(consent_id),
ADD
	KEY idx_consent_user(user_id),
ADD
	KEY idx_consent_type(consent_type);
ALTER TABLE
	reports
ADD
	PRIMARY KEY(report_id),
ADD
	KEY fk_reports_reporter(reporter_id),
ADD
	KEY fk_reports_resolver(resolved_by),
ADD
	KEY idx_report_status(status),
ADD
	KEY idx_report_content(content_type, content_id);
ALTER TABLE
	subs
ADD
	PRIMARY KEY(sub_id),
ADD
	UNIQUE KEY slug(slug),
ADD
	UNIQUE KEY name(name) USING HASH,
ADD
	KEY idx_sub_slug(slug(191)),
ADD
	KEY idx_sub_name(name(191));
ALTER TABLE
	tags
ADD
	PRIMARY KEY(tag_id),
ADD
	UNIQUE KEY slug(slug),
ADD
	UNIQUE KEY name(name) USING HASH,
ADD
	KEY idx_tag_slug(slug(191)),
ADD
	KEY idx_tag_usage(usage_count);
ALTER TABLE
	uploads
ADD
	PRIMARY KEY(upload_id),
ADD
	KEY idx_upload_user(user_id),
ADD
	KEY idx_upload_meme(meme_id),
ADD
	KEY idx_upload_type(file_type);
ALTER TABLE
	users
ADD
	PRIMARY KEY(user_id),
ADD
	UNIQUE KEY handle(handle),
ADD
	UNIQUE KEY email(email),
ADD
	KEY idx_user_handle(handle),
ADD
	KEY idx_user_email(email),
ADD
	KEY idx_user_display_name(display_name(191));
ALTER TABLE
	user_interactions
ADD
	PRIMARY KEY(
		user_id,
		interaction_type,
		content_type,
		content_id
	),
ADD
	KEY idx_interactions_content(content_type, content_id),
ADD
	KEY idx_interactions_user_type(user_id, interaction_type);
ALTER TABLE
	user_sessions
ADD
	PRIMARY KEY(session_id),
ADD
	KEY idx_session_user(user_id),
ADD
	KEY idx_session_expires(expires_at);
ALTER TABLE
	user_subs
ADD
	PRIMARY KEY(user_id, sub_id),
ADD
	KEY idx_user_subs_sub(sub_id);
ALTER TABLE
	subs
MODIFY
	sub_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE
	tags
MODIFY
	tag_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	AUTO_INCREMENT = 21;
ALTER TABLE
	users
MODIFY
	user_id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	AUTO_INCREMENT = 5;
ALTER TABLE
	comments
ADD
	CONSTRAINT fk_comments_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_comments_parent FOREIGN KEY(parent_id) REFERENCES comments(comment_id) ON DELETE
SET
	NULL,
ADD
	CONSTRAINT fk_comments_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	follows
ADD
	CONSTRAINT fk_follows_follower FOREIGN KEY(follower_id) REFERENCES users(user_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_follows_following FOREIGN KEY(following_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	memes
ADD
	CONSTRAINT fk_memes_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	meme_subs
ADD
	CONSTRAINT fk_meme_subs_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_meme_subs_sub FOREIGN KEY(sub_id) REFERENCES subs(sub_id) ON DELETE CASCADE;
ALTER TABLE
	meme_tags
ADD
	CONSTRAINT fk_meme_tags_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_meme_tags_tag FOREIGN KEY(tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE;
ALTER TABLE
	notifications
ADD
	CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	privacy_consents
ADD
	CONSTRAINT fk_privacy_consents_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	reports
ADD
	CONSTRAINT fk_reports_reporter FOREIGN KEY(reporter_id) REFERENCES users(user_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_reports_resolver FOREIGN KEY(resolved_by) REFERENCES users(user_id) ON DELETE
SET
	NULL;
ALTER TABLE
	uploads
ADD
	CONSTRAINT fk_uploads_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE
SET
	NULL,
ADD
	CONSTRAINT fk_uploads_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	user_interactions
ADD
	CONSTRAINT fk_user_interactions_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	user_sessions
ADD
	CONSTRAINT fk_user_sessions_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
ALTER TABLE
	user_subs
ADD
	CONSTRAINT fk_user_subs_sub FOREIGN KEY(sub_id) REFERENCES subs(sub_id) ON DELETE CASCADE,
ADD
	CONSTRAINT fk_user_subs_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE;
COMMIT;