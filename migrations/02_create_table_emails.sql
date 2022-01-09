SET NAMES 'utf8';
USE github_subscriptions;

DROP TABLE IF EXISTS emails;

CREATE TABLE emails (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  checked tinyint(1) NOT NULL DEFAULT 0,
  valid tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;
