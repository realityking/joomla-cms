# Drop keys we no longer need
ALTER TABLE `#__users` DROP KEY `usertype`;
ALTER TABLE `#__session` DROP KEY `whosonline`;

# Drop unused columns
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
ALTER TABLE `#__user` DROP COLUMN `usertype`;

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
ALTER TABLE `#__user_usergroups_map` ENGINE=InnoDB;
ALTER TABLE `#__viewlevels` ENGINE=InnoDB;
ALTER TABLE `#__weblinks` ENGINE=InnoDB;

# Columns with boolean values should be TINYINT
ALTER TABLE `#__languages` MODIFY `published` TINYINT unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__update_sites` MODIFY `enabled` TINYINT unsigned NOT NULL DEFAULT '0';

# The userid is a MEDIUMINT unsigned.
ALTER TABLE `#__users` MODIFY `id` MEDIUMINT unsigned NOT NULL AUTO_INCREMENT;

## Deal with all the checked_out columns
ALTER TABLE `#__banners` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__banner_clients` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__categories` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__contact_details` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__content` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__extensions` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__finder_filters` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__menu` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__modules` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__newsfeeds` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__user_notes` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__weblinks` MODIFY `checked_out` MEDIUMINT unsigned DEFAULT NULL;

## Modify the created_by, modified_by columns
ALTER TABLE `#__categories` MODIFY `created_user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__categories` MODIFY `modified_user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__contact_details` MODIFY `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__contact_details` MODIFY `modified_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__content` MODIFY `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__content` MODIFY `modified_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__finder_filters` MODIFY `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__finder_filters` MODIFY `modified_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__newsfeeds` MODIFY `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__newsfeeds` MODIFY `modified_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__user_notes` MODIFY `created_user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__user_notes` MODIFY `modified_user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__weblinks` MODIFY `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__weblinks` MODIFY `modified_by` MEDIUMINT unsigned DEFAULT NULL;

## Modify all other columns that are FK to the user id
ALTER TABLE `#__contact_details` MODIFY `user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__messages` MODIFY `user_id_from` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__messages` MODIFY `user_id_to` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__messages_cfg` MODIFY `user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__session` MODIFY `userid` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__user_profiles` MODIFY `user_id` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__user_usergroup_map` MODIFY `user_id` MEDIUMINT unsigned DEFAULT NULL;

# Change columns to be NULL where this is the new default
UPDATE `#__banners` SET `checked_out` = NULL;
UPDATE `#__banner_clients` SET `checked_out` = NULL;
UPDATE `#__categories` SET `checked_out` = NULL;
UPDATE `#__contact_details` SET `checked_out` = NULL;
UPDATE `#__content` SET `checked_out` = NULL;
UPDATE `#__extensions` SET `checked_out` = NULL;
UPDATE `#__finder_filters` SET `checked_out` = NULL;
UPDATE `#__menu` SET `checked_out` = NULL;
UPDATE `#__modules` SET `checked_out` = NULL;
UPDATE `#__newsfeeds` SET `checked_out` = NULL;
UPDATE `#__user_notes` SET `checked_out` = NULL;
UPDATE `#__weblinks` SET `checked_out` = NULL;

UPDATE `#__categories` SET `created_user_id` = NULL WHERE `created_user_id` = 0;
UPDATE `#__categories` SET `modified_user_id` = NULL WHERE `modified_user_id` = 0;
UPDATE `#__contact_details` SET `created_by` = NULL WHERE `created_by` = 0;
UPDATE `#__contact_details` SET `modified_by` = NULL WHERE `modified_by` = 0;
UPDATE `#__content` SET `created_by` = NULL WHERE `created_by` = 0;
UPDATE `#__content` SET `modified_by` = NULL WHERE `modified_by` = 0;
UPDATE `#__finder_filters` SET `created_by` = NULL WHERE `created_by` = 0;
UPDATE `#__finder_filters` SET `modified_by` = NULL WHERE `modified_by` = 0;
UPDATE `#__newsfeeds` SET `created_by` = NULL WHERE `created_by` = 0;
UPDATE `#__newsfeeds` SET `modified_by` = NULL WHERE `modified_by` = 0;
UPDATE `#__user_notes` SET `created_user_id` = NULL WHERE `created_user_id` = 0;
UPDATE `#__user_notes` SET `modified_user_id` = NULL WHERE `modified_user_id` = 0;
UPDATE `#__weblinks` SET `created_by` = NULL WHERE `created_by` = 0;
UPDATE `#__weblinks` SET `modified_by` = NULL WHERE `modified_by` = 0;

# extension_id should be an MEDIUMINT unsigned
ALTER TABLE `#__extensions` MODIFY `extension_id` MEDIUMINT unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__menu` MODIFY `component_id` MEDIUMINT unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__schemas` MODIFY `extension_id` MEDIUMINT unsigned NOT NULL;
ALTER TABLE `#__update_sites_extensions` MODIFY `extension_id` MEDIUMINT unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__updates` MODIFY `extension_id` MEDIUMINT unsigned NOT NULL DEFAULT '0';

# Add new columns
ALTER TABLE `#__weblinks` ADD COLUMN `version` INT(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__weblinks` ADD COLUMN `images` TEXT NOT NULL;
ALTER TABLE `#__newsfeeds` ADD COLUMN `description` TEXT NOT NULL;
ALTER TABLE `#__newsfeeds` ADD COLUMN `version` INT(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__newsfeeds` ADD COLUMN `hits` INT(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__newsfeeds` ADD COLUMN `images` TEXT NOT NULL;
ALTER TABLE `#__contact_details` ADD COLUMN `version` INT(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__contact_details` ADD COLUMN `hits` INT(10) unsigned NOT NULL DEFAULT '0';
ALTER TABLE `#__banners` ADD COLUMN `created_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__banners` ADD COLUMN `created_by_alias` varchar(255) NOT NULL DEFAULT '';
ALTER TABLE `#__banners` ADD COLUMN `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `#__banners` ADD COLUMN `modified_by` MEDIUMINT unsigned DEFAULT NULL;
ALTER TABLE `#__banners` ADD COLUMN `version` INT(10) unsigned NOT NULL DEFAULT '1';
ALTER TABLE `#__categories` ADD COLUMN `version` INT(10) unsigned NOT NULL DEFAULT '1';
