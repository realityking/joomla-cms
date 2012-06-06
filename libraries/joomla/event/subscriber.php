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
	static function getSubscribedEvents();
}
