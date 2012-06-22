<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.whosonline
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! System Remember Me Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.whosonline
 */
class plgSystemWhosonline extends JPlugin
{
	function onAfterInitialise()
	{
		$db      = JFactory::getDBO();
		$session = JFactory::getSession();

		// Remove expired sessions from the database.
		// The modulus introduces a little entropy, making the flushing less accurate
		// but fires the query less than half the time.
		$time = time();
		if ($time % 2)
		{
			$query = $db->getQuery(true);
			$query->delete($query->qn('#__session'))
				->where($query->qn('time') . ' < ' . $query->q((int) ($time - $session->getExpire())));

			$db->setQuery($query);
			try
			{
				$db->execute();
			}
			catch (RunTimeException $e)
			{
				return false;
			}
			return true;
		}
	}

	/*
	 * Old sessions are flushed based on the configuration value for the cookie
	 * lifetime. If an existing session, then the last access time is updated.
	 * If a new session, a session id is generated and a record is created in
	 * the #__sessions table.
	 */
	function onAfterSessionStart()
	{
		$session = JFactory::getSession();

		if (!$session->isActive())
		{
			return;
		}

		$handler = $session->storeName;
		if (($handler != 'database' && ($time % 2 || $session->isNew()))
			|| ($handler == 'database' && $session->isNew()))
		{
			$db   = JFactory::getDBO();
			$user = JFactory::getUser();
			$app  = JFactory::getApplication();

			$query = $db->getQuery(true);
			$query->select($query->qn('session_id'))
				->from($query->qn('#__session'))
				->where($query->qn('session_id') . ' = ' . $query->q($session->getId()));

			$db->setQuery($query, 0, 1);
			$exists = $db->loadResult();

			// If the session record doesn't exist initialise it.
			if (!$exists)
			{
				$query->clear();
				if ($session->isNew())
				{
					$query->insert($query->qn('#__session'))
						->columns($query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('time'))
						->values($query->q($session->getId()) . ', ' . (int) $app->getClientId() . ', ' . $query->q((int) time()));
					$db->setQuery($query);
				}
				else
				{
					$query->insert($query->qn('#__session'))
						->columns(
							$query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('guest') . ', ' .
							$query->qn('time') . ', ' . $query->qn('userid') . ', ' . $query->qn('username')
						)
						->values(
							$query->q($session->getId()) . ', ' . (int) $app->getClientId() . ', ' . (int) $user->get('guest') . ', ' .
							$query->q((int) $session->get('session.timer.start')) . ', ' . (int) $user->get('id') . ', ' . $query->q($user->get('username'))
						);

					$db->setQuery($query);
				}

				// If the insert failed, exit the application.
				try
				{
					$db->execute();
				}
				catch (RuntimeException $e)
				{
					die($e->getMessage());
				}
			}
		}
	}
}
