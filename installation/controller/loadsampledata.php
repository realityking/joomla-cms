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
 * Controller class to load the sample data for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerLoadsampledata extends JControllerBase
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

		// Get the options from the session.
		$data = $model->storeOptions($data);

		// Get the database model.
		$database = new InstallationModelDatabase;

		// Attempt to initialise the database.
		$return = $database->installSampleData($data);

		// Check if the database was initialised
		if (!$return)
		{
			/*
			 * The installSampleData method enqueued all messages for us, so we just need to
			 * redirect back to the site setup screen.
			 */
			$r->view = 'site';
			$app->sendJsonResponse($r);
			return false;
		}
		else
		{
			$r->sampleDataLoaded = 'true';
			$app->sendJsonResponse($r);
			return true;
		}
	}
}
