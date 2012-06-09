ALTER TABLE `#__redirect_links` ADD `hits` mediumint UNSIGNED NOT NULL DEFAULT '0' AFTER `comment`;
ALTER TABLE `#__users` ADD COLUMN `lastResetTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date of last password reset';
ALTER TABLE `#__users` ADD COLUMN `resetCount` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Count of password resets since lastResetTime';
ALTER TABLE `#__session` DROP KEY `whosonline`;