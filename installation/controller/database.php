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
 * Controller class to setup the database for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerDatabase extends JControllerBase
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

		// Get the posted values from the request and validate them.
		$data = $app->input->post->get('jform', array(), 'array');
		$return = $model->validate($data, 'database');

		$r = new JObject;

		// Check for validation errors.
		if ($return === false)
		{
			// Store the options in the session.
			$model->storeOptions($data);

			/*
			 * The validate method enqueued all messages for us, so we just need to
			 * redirect back to the database setup screen.
			 */
			$r->view = 'database';
			$app->sendJsonResponse($r);
			return false;
		}

		// Store the options in the session.
		$vars = $model->storeOptions($return);

		// Get the database model.
		$database = new InstallationModelDatabase;

		// Attempt to initialise the database.
		$return = $database->initialise($vars);

		// Check if the database was initialised
		if (!$return)
		{
			/*
			 * The initialise method enqueued all messages for us, so we just need to
			 * redirect back to the database setup screen.
			 */
			$r->view = 'database';
			$app->sendJsonResponse($r);
			return false;
		}
		else
		{
			// Mark sample content as not installed yet
			$data = array(
				'sample_installed' => '0'
			);
			$model->storeOptions($data);

			$r->view = 'filesystem';
			$app->sendJsonResponse($r);
			return true;
		}
	}
}
