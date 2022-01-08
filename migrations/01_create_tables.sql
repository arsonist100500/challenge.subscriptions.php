SET NAMES 'utf8';

USE github_subscriptions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS emails;

USE github_subscriptions;

CREATE TABLE emails (
  email varchar(128) NOT NULL,
  checked tinyint(1) NOT NULL DEFAULT 0,
  valid tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (email)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;

CREATE TABLE users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(64) NOT NULL,
  email varchar(64) NOT NULL,
  validts int(11) UNSIGNED NOT NULL DEFAULT 0,
  confirmed tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;
