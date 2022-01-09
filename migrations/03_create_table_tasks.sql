SET NAMES 'utf8';
USE github_subscriptions;

DROP TABLE IF EXISTS tasks;

CREATE TABLE tasks (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(64) NOT NULL,
  started datetime NULL,
  finished datetime NULL,
  result json NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_username (username)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;
