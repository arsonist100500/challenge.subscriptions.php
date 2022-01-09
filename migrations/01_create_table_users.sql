SET NAMES 'utf8';
USE github_subscriptions;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(64) NOT NULL,
  email varchar(128) NOT NULL,
  validts int(11) UNSIGNED NOT NULL DEFAULT 0,
  confirmed tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;
