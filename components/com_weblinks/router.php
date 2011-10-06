<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

 /* Weblinks Component Route Helper
 *
 * @package		Joomla.Site
 * @subpackage	com_weblinks
 * @since 1.6
 */

defined('_JEXEC') or die;

jimport('joomla.application.categories');

/**
 * Weblinks Router
 * 
 * 
 *
 */
class WeblinksRouter extends JComponentRouter
{
	function __construct()
	{
		$this->register('categories', 'categories');
		$this->register('category', 'category', 'id', 'categories', '', true);
		$this->register('weblink', 'weblink', 'id', 'category', 'catid');
		parent::__construct();
	}
}