<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Event
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Event subscriber interface
 *
 * @package     Joomla.Platform
 * @subpackage  Event
 * @since       12.2
 */
interface JEventSubscriber
{
	/**
	 * Returns an array of event names the subscriber wants to listen to.
	 *
	 * For example: array('eventName' => 'methodName')
	 *
	 * @return  array  The events to listen to.
     *
	 * @since   12.2
	 */
	public static function getSubscribedEvents();
}
