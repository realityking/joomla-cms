# Remove unnecessary keys
ALTER TABLE `#__users` DROP KEY `usertype`;
ALTER TABLE `#__session` DROP KEY `whosonline`;

# Remove unused table
DROP TABLE IF EXISTS `#__update_categories`;

# Remove unused columns
ALTER TABLE `#__contact_details` DROP `imagepos`;
ALTER TABLE `#__content` DROP COLUMN `title_alias`;
ALTER TABLE `#__content` DROP COLUMN `sectionid`;
ALTER TABLE `#__content` DROP COLUMN `mask`;
ALTER TABLE `#__content` DROP COLUMN `parentid`;
ALTER TABLE `#__newsfeeds` DROP COLUMN `filename`;
ALTER TABLE `#__weblinks` DROP COLUMN `sid`;
ALTER TABLE `#__weblinks` DROP COLUMN `date`;
ALTER TABLE `#__weblinks` DROP COLUMN `archived`;
ALTER TABLE `#__weblinks` DROP COLUMN `approved`;
ALTER TABLE `#__menu` DROP COLUMN `ordering`;
ALTER TABLE `#__session` DROP COLUMN `usertype`;
ALTER TABLE `#__users` DROP COLUMN `usertype`;
ALTER TABLE `#__updates` DROP COLUMN `categoryid`;

# Unprotect a number of extensions
UPDATE `#__extensions` SET protected = 0 WHERE
`name` = 'com_search' OR
`name` = 'mod_articles_archive' OR
`name` = 'mod_articles_latest' OR
`name` = 'mod_banners' OR
`name` = 'mod_feed' OR
`name` = 'mod_footer' OR
`name` = 'mod_users_latest' OR
`name` = 'mod_articles_category' OR
`name` = 'mod_articles_categories' OR
`name` = 'plg_content_pagebreak' OR
`name` = 'plg_content_pagenavigation' OR
`name` = 'plg_content_vote' OR
`name` = 'plg_editors_tinymce' OR
`name` = 'plg_system_p3p' OR
`name` = 'plg_user_contactcreator' OR
`name` = 'plg_user_profile';

# Columns with boolean values should be a tinyint
ALTER TABLE `#__languages` MODIFY `published` tinyint(1) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__update_sites` MODIFY `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0';

# extension_id should be a mediumint unsigned
ALTER TABLE `#__extensions` MODIFY `extension_id` mediumint unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__menu` MODIFY `component_id` mediumint unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__schemas` MODIFY `extension_id` mediumint unsigned NOT NULL;
ALTER TABLE `#__update_sites_extensions` MODIFY `extension_id` mediumint unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__updates` MODIFY `extension_id` mediumint unsigned NOT NULL DEFAULT '0';

# update_site_id should be a mediumint unsigned
ALTER TABLE `#__update_sites` MODIFY `update_site_id` mediumint unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__update_sites_extensions` MODIFY `update_site_id` mediumint unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__updates` MODIFY `update_site_id` mediumint unsigned NOT NULL DEFAULT '0';

# #__content.id is an int unsigned
ALTER TABLE `#__content` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__content_frontpage` MODIFY `content_id` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__content_rating` MODIFY `content_id` int unsigned NOT NULL DEFAULT '0';

# #__categories.id should be an int unsigned
ALTER TABLE `#__categories` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__banners` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__contact_details` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__content` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__user_notes` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__weblinks` MODIFY `catid` int unsigned NOT NULL DEFAULT '0';

# #__menu.id should be an int unsigned
ALTER TABLE `#__menu` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__menu` MODIFY `parent_id` int unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__modules_menu` MODIFY `menuid` int unsigned NOT NULL DEFAULT '0';

# #__modules.id should be an int unsigned
ALTER TABLE `#__modules` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__modules_menu` MODIFY `moduleid` int unsigned NOT NULL DEFAULT '0';

# #__usergroups.id should be an int unsigned
ALTER TABLE `#__usergroups` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__user_usergroup_map` MODIFY `group_id` int unsigned DEFAULT '0';

# Other id's should be unsigned too
ALTER TABLE `#__updates` MODIFY `update_id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__overrider` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__contact_details` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;

# The userid should be an int unsigned
ALTER TABLE `#__users` MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__user_profiles` MODIFY `user_id` int unsigned NOT NULL;
ALTER TABLE `#__messages` MODIFY `user_id_from` int unsigned DEFAULT '0';
ALTER TABLE `#__messages` MODIFY `user_id_to` int unsigned DEFAULT '0';
ALTER TABLE `#__messages_cfg` MODIFY `user_id` int unsigned DEFAULT '0';
ALTER TABLE `#__session` MODIFY `userid` int unsigned DEFAULT '0';
ALTER TABLE `#__user_usergroup_map` MODIFY `user_id` int unsigned DEFAULT '0';
ALTER TABLE `#__user_notes` MODIFY `user_id` int unsigned DEFAULT '0';

## Deal with all the checked_out columns
ALTER TABLE `#__banners` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__banner_clients` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__categories` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__contact_details` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__content` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__extensions` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__finder_filters` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__menu` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__modules` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__user_notes` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__weblinks` MODIFY `checked_out` int unsigned NOT NULL DEFAULT '0';

## Modify the created_by, modified_by columns
ALTER TABLE `#__categories` MODIFY `created_user_id` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__categories` MODIFY `modified_user_id` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__contact_details` MODIFY `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__contact_details` MODIFY `modified_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__content` MODIFY `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__content` MODIFY `modified_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__finder_filters` MODIFY `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__finder_filters` MODIFY `modified_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` MODIFY `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` MODIFY `modified_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__user_notes` MODIFY `created_user_id` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__user_notes` MODIFY `modified_user_id` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__weblinks` MODIFY `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__weblinks` MODIFY `modified_by` int unsigned NOT NULL DEFAULT '0';

# Change most tables to InnoDB
ALTER TABLE `#__assets` ENGINE=InnoDB;
ALTER TABLE `#__associations` ENGINE=InnoDB;
ALTER TABLE `#__banners` ENGINE=InnoDB;
ALTER TABLE `#__banner_clients` ENGINE=InnoDB;
ALTER TABLE `#__banner_tracks` ENGINE=InnoDB;
ALTER TABLE `#__categories` ENGINE=InnoDB;
ALTER TABLE `#__contact_details` ENGINE=InnoDB;
ALTER TABLE `#__content` ENGINE=InnoDB;
ALTER TABLE `#__content_frontpage` ENGINE=InnoDB;
ALTER TABLE `#__content_rating` ENGINE=InnoDB;
ALTER TABLE `#__core_log_searches` ENGINE=InnoDB;
ALTER TABLE `#__extensions` ENGINE=InnoDB;
ALTER TABLE `#__finder_filters` ENGINE=InnoDB;
ALTER TABLE `#__finder_links` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms0` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms1` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms2` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms3` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms4` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms5` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms6` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms7` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms8` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_terms9` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termsa` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termsb` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termsc` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termsd` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termse` ENGINE=InnoDB;
ALTER TABLE `#__finder_links_termsf` ENGINE=InnoDB;
ALTER TABLE `#__finder_taxonomy` ENGINE=InnoDB;
ALTER TABLE `#__finder_taxonomy_map` ENGINE=InnoDB;
ALTER TABLE `#__finder_terms` ENGINE=InnoDB;
ALTER TABLE `#__finder_terms_common` ENGINE=InnoDB;
ALTER TABLE `#__finder_types` ENGINE=InnoDB;
ALTER TABLE `#__languages` ENGINE=InnoDB;
ALTER TABLE `#__menu` ENGINE=InnoDB;
ALTER TABLE `#__menu_types` ENGINE=InnoDB;
ALTER TABLE `#__messages` ENGINE=InnoDB;
ALTER TABLE `#__messages_cfg` ENGINE=InnoDB;
ALTER TABLE `#__modules` ENGINE=InnoDB;
ALTER TABLE `#__modules_menu` ENGINE=InnoDB;
ALTER TABLE `#__newsfeeds` ENGINE=InnoDB;
ALTER TABLE `#__overrider` ENGINE=InnoDB;
ALTER TABLE `#__redirect_links` ENGINE=InnoDB;
ALTER TABLE `#__schemas` ENGINE=InnoDB;
ALTER TABLE `#__session` ENGINE=InnoDB;
ALTER TABLE `#__template_styles` ENGINE=InnoDB;
ALTER TABLE `#__updates` ENGINE=InnoDB;
ALTER TABLE `#__update_categories` ENGINE=InnoDB;
ALTER TABLE `#__update_sites` ENGINE=InnoDB;
ALTER TABLE `#__update_sites_extensions` ENGINE=InnoDB;
ALTER TABLE `#__users` ENGINE=InnoDB;
ALTER TABLE `#__usergroups` ENGINE=InnoDB;
ALTER TABLE `#__user_notes` ENGINE=InnoDB;
ALTER TABLE `#__user_profiles` ENGINE=InnoDB;
ALTER TABLE `#__user_usergroup_map` ENGINE=InnoDB;
ALTER TABLE `#__viewlevels` ENGINE=InnoDB;
ALTER TABLE `#__weblinks` ENGINE=InnoDB;

# Add new columns to normalise our content tables
ALTER TABLE `#__weblinks` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__weblinks` ADD COLUMN `images` text NOT NULL;
ALTER TABLE `#__newsfeeds` ADD COLUMN `description` text NOT NULL;
ALTER TABLE `#__newsfeeds` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__newsfeeds` ADD COLUMN `hits` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` ADD COLUMN `images` text NOT NULL;
ALTER TABLE `#__contact_details` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__contact_details` ADD COLUMN `hits` int(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__banners` ADD COLUMN `created_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__banners` ADD COLUMN `created_by_alias` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__banners` ADD COLUMN `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__banners` ADD COLUMN `modified_by` int unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__banners` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__categories` ADD COLUMN `version` int(10) unsigned NOT NULL DEFAULT '1';

# Add columns for improved language support in finder.
ALTER TABLE `#__finder_terms` ADD COLUMN `language` char(3) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_tokens` ADD COLUMN `language` char(3) NOT NULL DEFAULT '';
ALTER TABLE `#__finder_tokens_aggregate` ADD COLUMN `language` char(3) NOT NULL DEFAULT '';

# Add new templates
INSERT INTO `#__extensions`
	(`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)
	VALUES
	('isis', 'template', 'isis', '', 1, 1, 1, 0, '{"name":"isis","type":"template","creationDate":"3\\/30\\/2012","author":"Kyle Ledbetter","copyright":"Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"","version":"1.0","description":"TPL_ISIS_XML_DESCRIPTION","group":""}', '{"templateColor":"","logoFile":""}', '', '', 0, '0000-00-00 00:00:00', 0, 0),
	('protostar', 'template', 'protostar', '', 0, 1, 1, 0, '{"name":"protostar","type":"template","creationDate":"4\\/30\\/2012","author":"Kyle Ledbetter","copyright":"Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.","authorEmail":"admin@joomla.org","authorUrl":"","version":"1.0","description":"TPL_PROTOSTAR_XML_DESCRIPTION","group":""}', '{"templateColor":"","logoFile":"","googleFont":"1","googleFontName":"Open+Sans","fluidContainer":"0"}', '', '', 0, '0000-00-00 00:00:00', 0, 0);

INSERT INTO `#__template_styles` (`template`, `client_id`, `home`, `title`, `params`) VALUES
	('protostar', 0, '0', 'protostar - Default', '{"templateColor":"","logoFile":"","googleFont":"1","googleFontName":"Open+Sans","fluidContainer":"0"}'),
	('isis', 1, '1', 'isis - Default', '{"templateColor":"","logoFile":""}');

# Deal with removed templates
DELETE FROM `#__extensions`
	WHERE type = 'template' AND name = 'bluestork';

DELETE FROM `#__template_styles`
	WHERE template = 'bluestork';

UPDATE `#__template_styles`
SET home = (CASE WHEN (SELECT count FROM (SELECT count(`id`) AS count
			FROM `#__template_styles`
			WHERE home = '1'
			AND client_id = 1) as c) = 0
			THEN '1'
			ELSE '0'
			END)
WHERE template = 'isis'
AND home != '1';
