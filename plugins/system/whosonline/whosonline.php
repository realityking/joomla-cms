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
 * @subpackage  System.whosonline
 * @since       3.0
 */
class plgSystemWhosonline extends JPlugin
{
	/**
	 * Remove all sessions for the user name
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array    $user    Holds the user data
	 * @param   boolean  $succes  True if user was succesfully stored in the database
	 * @param   string   $msg     Message
	 *
	 * @return	boolean
	 *
	 * @since	3.0
	 */
	public function onUserAfterDelete($user, $succes, $msg)
	{
		if (!$succes) {
			return false;
		}

		$db = JFactory::getDbo();
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('userid') . ' = ' . (int) $user['id']
		);
		$db->execute();

		return true;
	}

	/*
	 * We flush the expired records after initialisation.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	function onAfterInitialise()
	{
		// Remove expired sessions from the database.
		// The modulus introduces a little entropy, making the flushing less accurate
		// but fires the query less than half the time.
		$time = time();
		if ($time % 2)
		{
			$db      = JFactory::getDBO();
			$session = JFactory::getSession();

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
	 *
	 * @return  void
	 *
	 * @since   3.0
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

	/**
	 * This method should handle any login logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data
	 * @param   array  $options  Array holding options (remember, autoregister, group)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.0
	 */
	public function onUserLogin($user, $options = array())
	{
		$instance = JUser::getInstance();
		$id = (int) JUserHelper::getUserId($user['username']);
		if (!$id)
		{
			throw new RuntimeException('The user with the name: ' . $user['username'] . ' doesn\'t exist.');
		}

		$instance->load($id);

		$db = JFactory::getDBO();
		$session = JFactory::getSession();

		// Update the user related fields for the Joomla sessions table.
		$db->setQuery(
			'UPDATE ' . $db->quoteName('#__session') .
			' SET ' . $db->quoteName('guest') . ' = ' . $db->quote($instance->get('guest')) . ',' .
			'	' . $db->quoteName('username') . ' = ' . $db->quote($instance->get('username')) . ',' .
			'	' . $db->quoteName('userid') . ' = ' . (int) $instance->get('id') .
			' WHERE ' . $db->quoteName('session_id') . ' = ' . $db->quote($session->getId())
		);
		$db->execute();

		return true;
	}

	/**
	 * This method should handle any logout logic and report back to the subject
	 *
	 * @param   array  $user     Holds the user data.
	 * @param   array  $options  Array holding options (client, ...).
	 *
	 * @return	boolean  True on success
	 *
	 * @since	3.0
	 */
	public function onUserLogout($user, $options = array())
	{
		// Force logout all users with that userid
		$db = JFactory::getDBO();
		$db->setQuery(
			'DELETE FROM ' . $db->quoteName('#__session') .
			' WHERE ' . $db->quoteName('userid').' = ' . (int) $user['id'] .
			' AND ' . $db->quoteName('client_id').' = ' . (int) $options['clientid']
		);
		$db->execute();

		return true;
	}
}
