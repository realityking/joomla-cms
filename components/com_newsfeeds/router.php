<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 /* Newsfeeds Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_newsfeeds
 * @since 1.6
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');
JLoader::register('JComponentRouter', JPATH_LIBRARIES.'/joomla/application/component/router.php');

/**
 * Newsfeed Router
 * 
 * 
 *
 */
class NewsfeedRouter extends JComponentRouter implements JComponentRouterInterface
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('newsfeed', 'newsfeed', 'id', 'category', 'catid');
		parent::__construct();
	}
}