CREATE DATABASE basedmeme CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE basedmeme;
CREATE TABLE users(
	user_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	handle VARCHAR(32) NOT NULL UNIQUE,
	email VARCHAR(254) NOT NULL UNIQUE,
	password_hash VARCHAR(64) NOT NULL,
	display_name VARCHAR(1024),
	bio TEXT,
	avatar VARCHAR(260),
	location VARCHAR(128),
	joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	is_verified BOOLEAN DEFAULT FALSE,
	is_private BOOLEAN DEFAULT FALSE,
	is_admin BOOLEAN DEFAULT FALSE,
	is_banned BOOLEAN DEFAULT FALSE,
	is_active BOOLEAN DEFAULT TRUE,
	INDEX idx_user_handle(handle),
	INDEX idx_user_email(email),
	INDEX idx_user_display_name(display_name(191))
);
CREATE TABLE subs(
	sub_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(1024) NOT NULL UNIQUE,
	slug VARCHAR(256) NOT NULL UNIQUE,
	description TEXT,
	icon VARCHAR(260),
	user_count INT UNSIGNED DEFAULT 0,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX idx_sub_slug(slug(191)),
	INDEX idx_sub_name(name(191))
);
CREATE TABLE tags(
	tag_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(1024) NOT NULL UNIQUE,
	slug VARCHAR(256) NOT NULL UNIQUE,
	usage_count INT UNSIGNED DEFAULT 0,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	INDEX idx_tag_slug(slug(191)),
	INDEX idx_tag_usage(usage_count)
);
CREATE TABLE memes(
	meme_id CHAR(11) PRIMARY KEY,
	slug VARCHAR(256) NOT NULL UNIQUE,
	user_id BIGINT UNSIGNED NOT NULL,
	title VARCHAR(512) NOT NULL,
	content TEXT,
	media_url TEXT,
	status ENUM ('draft', 'published', 'archived') DEFAULT 'draft',
	visibility ENUM('public', 'private', 'followers') DEFAULT 'public',
	published_at TIMESTAMP NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	view_count INT UNSIGNED DEFAULT 0,
	like_count INT UNSIGNED DEFAULT 0,
	dislike_count INT UNSIGNED DEFAULT 0,
	upvote_count INT UNSIGNED DEFAULT 0,
	comment_count INT UNSIGNED DEFAULT 0,
	share_count INT UNSIGNED DEFAULT 0,
	saved_count INT UNSIGNED DEFAULT 0,
	allow_comments BOOLEAN DEFAULT TRUE,
	CONSTRAINT fk_memes_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_meme_slug(slug(191)),
	INDEX idx_meme_user(user_id),
	INDEX idx_meme_status(status),
	INDEX idx_meme_visibility(visibility),
	INDEX idx_meme_published_at(published_at),
	INDEX idx_meme_popularity(
		view_count,
		like_count,
		comment_count
	)
);
CREATE TABLE uploads(
	upload_id CHAR(11) PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	file_name VARCHAR(260) NOT NULL,
	file_path VARCHAR(260) NOT NULL,
	file_type VARCHAR(32) NOT NULL,
	file_size INT UNSIGNED NOT NULL,
	meme_id CHAR(11),
	width INT UNSIGNED,
	height INT UNSIGNED,
	duration INT UNSIGNED,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_uploads_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	CONSTRAINT fk_uploads_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE
	SET
		NULL,
		INDEX idx_upload_user(user_id),
		INDEX idx_upload_meme(meme_id),
		INDEX idx_upload_type(file_type)
);
CREATE TABLE comments(
	comment_id CHAR(11) PRIMARY KEY,
	meme_id CHAR(11) NOT NULL,
	user_id BIGINT UNSIGNED NOT NULL,
	content TEXT NOT NULL,
	parent_id CHAR(11),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	like_count INT UNSIGNED DEFAULT 0,
	dislike_count INT UNSIGNED DEFAULT 0,
	upvote_count INT UNSIGNED DEFAULT 0,
	is_pinned BOOLEAN DEFAULT FALSE,
	CONSTRAINT fk_comments_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
	CONSTRAINT fk_comments_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	CONSTRAINT fk_comments_parent FOREIGN KEY(parent_id) REFERENCES comments(comment_id) ON DELETE
	SET
		NULL,
		INDEX idx_comment_meme(meme_id),
		INDEX idx_comment_user(user_id),
		INDEX idx_comment_parent(parent_id),
		INDEX idx_comment_popularity(like_count, upvote_count)
);
CREATE TABLE follows(
	follower_id BIGINT UNSIGNED NOT NULL,
	following_id BIGINT UNSIGNED NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(follower_id, following_id),
	CONSTRAINT fk_follows_follower FOREIGN KEY(follower_id) REFERENCES users(user_id) ON DELETE CASCADE,
	CONSTRAINT fk_follows_following FOREIGN KEY(following_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_follow_follower(follower_id),
	INDEX idx_follow_following(following_id)
);
CREATE TABLE meme_tags(
	meme_id CHAR(11) NOT NULL,
	tag_id BIGINT UNSIGNED NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(meme_id, tag_id),
	CONSTRAINT fk_meme_tags_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
	CONSTRAINT fk_meme_tags_tag FOREIGN KEY(tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE,
	INDEX idx_meme_tags_tag(tag_id)
);
CREATE TABLE meme_subs(
	meme_id CHAR(11) NOT NULL,
	sub_id BIGINT UNSIGNED NOT NULL,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(meme_id, sub_id),
	CONSTRAINT fk_meme_subs_meme FOREIGN KEY(meme_id) REFERENCES memes(meme_id) ON DELETE CASCADE,
	CONSTRAINT fk_meme_subs_sub FOREIGN KEY(sub_id) REFERENCES subs(sub_id) ON DELETE CASCADE,
	INDEX idx_meme_subs_sub(sub_id)
);
CREATE TABLE user_subs(
	user_id BIGINT UNSIGNED NOT NULL,
	sub_id BIGINT UNSIGNED NOT NULL,
	is_moderator BOOLEAN DEFAULT FALSE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(user_id, sub_id),
	CONSTRAINT fk_user_subs_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	CONSTRAINT fk_user_subs_sub FOREIGN KEY(sub_id) REFERENCES subs(sub_id) ON DELETE CASCADE,
	INDEX idx_user_subs_sub(sub_id)
);
CREATE TABLE user_interactions(
	user_id BIGINT UNSIGNED NOT NULL,
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
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(
		user_id,
		interaction_type,
		content_type,
		content_id
	),
	CONSTRAINT fk_user_interactions_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_interactions_content(content_type, content_id),
	INDEX idx_interactions_user_type(user_id, interaction_type)
);
CREATE TABLE reports(
	report_id CHAR(11) PRIMARY KEY,
	reporter_id BIGINT UNSIGNED NOT NULL,
	content_type ENUM('meme', 'comment', 'user') NOT NULL,
	content_id CHAR(11) NOT NULL,
	reason ENUM(
		'minor',
		'misinformation',
		'spam',
		'other'
	) NOT NULL,
	details TEXT,
	resolved_by BIGINT UNSIGNED,
	status ENUM (
		'pending',
		'reviewing',
		'resolved',
		'rejected'
	) DEFAULT 'pending',
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	resolved_at TIMESTAMP NULL,
	CONSTRAINT fk_reports_reporter FOREIGN KEY(reporter_id) REFERENCES users(user_id) ON DELETE CASCADE,
	CONSTRAINT fk_reports_resolver FOREIGN KEY(resolved_by) REFERENCES users(user_id) ON DELETE
	SET
		NULL,
		INDEX idx_report_status(status),
		INDEX idx_report_content(content_type, content_id)
);
CREATE TABLE privacy_consents(
	consent_id CHAR(11) PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	consent_type ENUM(
		'terms',
		'privacy',
		'cookies',
		'marketing',
		'third_party'
	) NOT NULL,
	is_granted BOOLEAN NOT NULL DEFAULT TRUE,
	ip_address VARCHAR(45),
	user_agent VARCHAR(512),
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	CONSTRAINT fk_privacy_consents_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_consent_user(user_id),
	INDEX idx_consent_type(consent_type)
);
CREATE TABLE notifications(
	notification_id CHAR(11) PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	type ENUM(
		'like',
		'comment',
		'follow',
		'mention',
		'system'
	) NOT NULL,
	content VARCHAR(512) NOT NULL,
	reference_id CHAR(11),
	is_read BOOLEAN DEFAULT FALSE,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_notifications_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_notification_user(user_id),
	INDEX idx_notification_read(is_read)
);
CREATE TABLE user_sessions(
	session_id CHAR(11) PRIMARY KEY,
	user_id BIGINT UNSIGNED NOT NULL,
	ip_address VARCHAR(45) NOT NULL,
	user_agent TEXT,
	created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	expires_at TIMESTAMP NOT NULL DEFAULT(CURRENT_TIMESTAMP + INTERVAL 1 DAY),
	last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	CONSTRAINT fk_user_sessions_user FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
	INDEX idx_session_user(user_id),
	INDEX idx_session_expires(expires_at)
);
ALTER TABLE
	users
ADD
	COLUMN birthday DATE NULL DEFAULT NULL
AFTER
	avatar,
ADD
	COLUMN sex VARCHAR(32) NULL DEFAULT NULL
AFTER
	birthday,
ADD
	COLUMN orientation VARCHAR(32) NULL DEFAULT NULL
AFTER
	sex,
ADD
	COLUMN pronouns VARCHAR(32) NULL DEFAULT NULL
AFTER
	orientation,
ADD
	COLUMN touch_grass VARCHAR(32) NULL DEFAULT NULL
AFTER
	pronouns,
ADD
	COLUMN meme_knowledge TEXT NULL DEFAULT NULL
AFTER
	touch_grass,
ADD
	COLUMN secret_question VARCHAR(255) NOT NULL
AFTER
	meme_knowledge,
ADD
	COLUMN secret_answer_hash VARCHAR(64) NOT NULL
AFTER
	secret_question;
INSERT INTO
	tags (name, slug)
VALUES
	('dank', 'dank'),
	('cursed', 'cursed'),
	('pov', 'pov'),
	('no cap fr fr', 'no-cap-fr-fr'),
	('riz z', 'rizz'),
	('sigma', 'sigma'),
	('goofy-ahh', 'goofy-ahh'),
	('touch grass', 'touch-grass'),
	('gyatt', 'gyatt'),
	('sus', 'sus'),
	('mid', 'mid'),
	('relatable', 'relatable'),
	('cringe', 'cringe'),
	('shitpost', 'shitpost'),
	('adgy', 'adgy'),
	('deep-fried', 'deep-fried'),
	('npc', 'npc'),
	('💀', 'skull'),
	('skidibi', 'skibidi'),
	('toilet', 'toilet') ON DUPLICATE KEY
UPDATE
	usage_count = usage_count + 1;
INSERT INTO
	users (
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
		'1',
		'test',
		'test@example.com',
		'$2y$10$gUh6tSwvezFvg3NPANFU7.Vy3WMu8VjXPZ/UZDzFoakBhR2SzdqkS',
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
		'$2y$10$O2pBjoVDEbQeEI3kCJeMl.CYzgZCPDtnsk6ZUG7eN3U2nPg4OP1du',
		NULL,
		'1970-01-01 01:00:01',
		'1970-01-01 01:00:01',
		'1970-01-01 01:00:01',
		'1',
		'0',
		'0',
		'0',
		'1'
	);
INSERT INTO
	memes (
		meme_id,
		slug,
		user_id,
		title,
		content,
		media_url,
		status,
		visibility,
		published_at
	)
VALUES
	(
		'abcdef12345',
		'lorem-picsum',
		1,
		'Lorem Picsum',
		'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
		'https://picsum.photos/400',
		'published',
		'public',
		CURRENT_TIMESTAMP
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
		CURRENT_TIMESTAMP
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
		CURRENT_TIMESTAMP
	),
	(
		'nbqMIBYJlvk',
		'liquid-trees',
		1,
		'Liquid Trees',
		"What's wrong with trees?",
		'https://images7.memedroid.com/images/UPLOADED915/6802ea339e30b.jpeg',
		'published',
		'public',
		CURRENT_TIMESTAMP
	);
INSERT INTO
	meme_tags (meme_id, tag_id)
SELECT
	'abcdef12345',
	tag_id
FROM
	tags
WHERE
	slug IN ('sus');
INSERT INTO
	meme_tags (meme_id, tag_id)
SELECT
	'r5MR7_INQwg',
	tag_id
FROM
	tags
WHERE
	slug IN ('shitpost', 'relatable', 'mid');
INSERT INTO
	meme_tags (meme_id, tag_id)
SELECT
	'MBJFPq2Llps',
	tag_id
FROM
	tags
WHERE
	slug IN ('cursed', 'shitpost', 'relatable', 'adgy');
INSERT INTO
	meme_tags (meme_id, tag_id)
SELECT
	'nbqMIBYJlvk',
	tag_id
FROM
	tags
WHERE
	slug IN ('shitpost', 'goofy-ahh', 'mid');
UPDATE
	tags
SET
	usage_count = (
		SELECT
			COUNT(*)
		FROM
			meme_tags
		WHERE
			meme_tags.tag_id = tags.tag_id
	);
ALTER TABLE
	memes
ADD
	COLUMN spicyness DECIMAL(3, 2) UNSIGNED NOT NULL DEFAULT 0.00
AFTER
	visibility;
ALTER TABLE
	reports CHANGE reason reason ENUM(
		'minor',
		'misinformation',
		'spam',
		'bug',
		'report',
		'suggestion',
		'collab',
		'other'
	) NOT NULL;