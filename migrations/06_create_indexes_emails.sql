SET NAMES 'utf8';
USE github_subscriptions;

ALTER TABLE `emails`
    ADD INDEX idx_email (email),
    ADD INDEX idx_checked_valid (checked, valid);
