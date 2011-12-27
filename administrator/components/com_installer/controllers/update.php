<?php
/**
 * @version		$Id$
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 */
class InstallerControllerUpdate extends JController
{
	/**
	 * Update a set of extensions.
	 *
	 * @since	1.6
	 */
	function update()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model	= $this->getModel('update');
		$uid	= JRequest::getVar('cid', array(), '', 'array');

		JArrayHelper::toInteger($uid, array());
		if ($model->update($uid)) {
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
		}

		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_installer.redirect_url');
		if(empty($redirect_url)) {
			$redirect_url = JRoute::_('index.php?option=com_installer&view=update',false);
		} else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_installer.redirect_url', '');
			$app->setUserState('com_installer.message', '');
			$app->setUserState('com_installer.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}

	/**
	 * Find new updates.
	 *
	 * @since	1.6
	 */
	function find()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		// Get the caching duration
		jimport('joomla.application.component.helper');
		$component = JComponentHelper::getComponent('com_installer');
		$params = $component->params;
		$cache_timeout = $params->getValue('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;
		// Find updates
		$model	= $this->getModel('update');
		$result = $model->findUpdates(0, $cache_timeout);
		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=update',false));
		//$view->display();
	}

	/**
	 * Purges updates.
	 *
	 * @since	1.6
	 */
	function purge()
	{
		// Purge updates
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getModel('update');
		$model->purge();
		$model->enableSites();
		$this->setRedirect(JRoute::_('index.php?option=com_installer&view=update',false), $model->_message);
	}
}
