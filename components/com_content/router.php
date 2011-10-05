<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');
JLoader::register('JComponentRouter', JPATH_LIBRARIES.'/joomla/application/component/router.php');

/**
 * Content Router
 * 
 * 
 *
 */
class ContentRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('article', 'article', 'id', 'category', 'catid');
		$this->register('archive', 'archive');
		$this->register('featured', 'featured');
		parent::__construct();
	}
}