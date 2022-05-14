DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `superusers`;

CREATE TABLE users(
	username varchar(50) PRIMARY KEY,
	password varchar(100) NOT NULL);


LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES ('admin',password('password'));
UNLOCK TABLES;

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
	postID int NOT NULL AUTO_INCREMENT,
	content varchar(280) NOT NULL,
	`owner` varchar(50),
	PRIMARY KEY (postID));


CREATE TABLE `comments` (
	commentID int NOT NULL AUTO_INCREMENT,
	content varchar(280) NOT NULL,
	`owner` varchar(50),
	pID int,
	PRIMARY KEY (commentID));

CREATE TABLE `superusers` (
	user varchar(20) PRIMARY KEY,
	password varchar(50) NOT NULL);

LOCK TABLES `superusers` WRITE;
INSERT INTO `superusers` VALUES ('admin', password('password'));
UNLOCK TABLES;
