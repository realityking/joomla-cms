<?php
/**
 * @package     Joomla.Installation
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Controller class to verify the FTP settings for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerVerifyftpsettings extends JControllerBase
{
	/**
	 * Execute the controller.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.0
	 */
	public function execute()
	{
		// Get the application object.
		$app = $this->getApplication();

		// Check for request forgeries.
		JSession::checkToken() or $app->sendJsonResponse(new Exception(JText::_('JINVALID_TOKEN'), 403));

		// Get the setup model.
		$model = new InstallationModelSetup;

		// Get the posted config options
		$data = $app->input->getVar('jform', array());

		// Store the options in the session.
		$vars = $model->storeOptions($data);

		// Get the filesystem model.
		$filesystem = new InstallationModelFilesystem;

		// Attempt to verify the FTP settings.
		$return = $filesystem->verifyFtpSettings($vars);

		$r = new JObject;

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The detectFtpRoot method enqueued all messages for us, so we just need to
			 * redirect back to the filesystem setup screen.
			 */
			$r->view = 'filesystem';
			$app->sendJsonResponse($r);
			return false;
		}

		// Set the valid setting and send the response
		$r->valid = $return;
		$app->sendJsonResponse($r);
		return true;
	}
}
