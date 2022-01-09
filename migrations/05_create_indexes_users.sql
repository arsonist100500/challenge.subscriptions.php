SET NAMES 'utf8';
USE github_subscriptions;

ALTER TABLE `users`
    ADD UNIQUE INDEX uk_username (username),
    ADD UNIQUE INDEX uk_email (email),
    ADD INDEX idx_validts (validts),
    ADD INDEX idx_confirmed (confirmed),
    ADD INDEX idx_notified (notified);
