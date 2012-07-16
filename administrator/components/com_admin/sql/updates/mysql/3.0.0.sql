# Placeholder file for database changes for version 3.0.0

UPDATE `#__extensions` SET protected = 0 WHERE
`name` = 'com_search' OR
`name` = 'mod_articles_archive' OR
`name` = 'mod_articles_latest' OR
`name` = 'mod_banners' OR
`name` = 'mod_feed' OR
`name` = 'mod_footer' OR
`name` = 'mod_users_latest' OR
`name` = 'com_mailto' OR
`name` = 'com_mailto' OR
`name` = 'mod_articles_category' OR
`name` = 'mod_articles_categories' OR
`name` = 'plg_content_pagebreak' OR
`name` = 'plg_content_pagenavigation' OR
`name` = 'plg_content_vote' OR
`name` = 'plg_editors_tinymce' OR
`name` = 'plg_system_p3p' OR
`name` = 'plg_user_contactcreator' OR
`name` = 'plg_user_profile';
