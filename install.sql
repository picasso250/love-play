CREATE TABLE `love_play`.`item` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `text` TEXT NULL,
  `create_time` TIMESTAMP NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `love_play`.`comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` INT UNSIGNED NOT NULL,
  `text` TEXT NOT NULL,
  `create_time` TIMESTAMP NULL,
  PRIMARY KEY (`id`));
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `love_play`.`item` 
ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `create_time`;
ALTER TABLE `love_play`.`item` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `love_play`.`comment` 
ADD COLUMN `user_id` INT UNSIGNED NULL AFTER `create_time`;
ALTER TABLE `love_play`.`comment` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `id`;
