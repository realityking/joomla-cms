<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.session
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.session
 */
class plgSystemSession extends JPlugin
{
	function onAfterSessionStart()
	{
		$session = JFactory::getSession();
		if ($session->isNew())
		{
			$session->set('registry', new JRegistry('session'));
			$session->set('user', new JUser);
		}
	}
}
