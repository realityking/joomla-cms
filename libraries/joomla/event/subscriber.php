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
	 * The resulting array has to have the format:
	 *  * array('eventName' => 'methodName')
	 *
	 * Currently the method name has to be the event name.
	 * A future version will remove this limitation.
	 *
	 * @return  array  Array of events/methods
	 *
	 * @since   12.2
	 */
	static function getSubscribedEvents();
}
