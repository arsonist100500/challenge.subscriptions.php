SET NAMES 'utf8';
USE github_subscriptions;

DROP TABLE IF EXISTS tasks;

CREATE TABLE tasks (
  id int(11) NOT NULL AUTO_INCREMENT,
  email varchar(128) NOT NULL,
  started datetime NULL,
  finished datetime NULL,
  input json NULL,
  result json NULL,
  PRIMARY KEY (id),
  INDEX idx_email (email)
)
ENGINE = INNODB
CHARACTER SET utf8
COLLATE utf8_general_ci
ROW_FORMAT = DYNAMIC;
