SET NAMES 'utf8';
USE github_subscriptions;

ALTER TABLE users ADD COLUMN notified DATETIME NULL AFTER confirmed;
