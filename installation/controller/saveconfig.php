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
 * Controller class to save the site configuration for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerSaveconfig extends JControllerBase
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
		$return = $model->validate($data, 'site');

		// Attempt to save the data before validation
		$form = $model->getForm();
		$data = $form->filter($data);
		unset($data['admin_password2']);
		$model->storeOptions($data);

		$r = new JObject;

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The validate method enqueued all messages for us, so we just need to
			 * redirect back to the language selection screen.
			 */
			$r->view = 'site';
			$app->sendJsonResponse($r);
			return false;
		}

		// Store the options in the session.
		unset($return['admin_password2']);
		$vars = $model->storeOptions($return);

		// Get the configuration model.
		$configuration = new InstallationModelConfiguration;

		// Attempt to initialise the database.
		$return = $configuration->setup($vars);

		// Check if the database was initialised
		if (!$return)
		{
			/*
			 * The setup method enqueued all messages for us, so we just need to
			 * redirect back to the site setup screen.
			 */
			$r->view = 'site';
			$app->sendJsonResponse($r);
			return false;
		}
		else
		{
			// Redirect to the next page.
			$r->view = 'complete';
			$app->sendJsonResponse($r);
			return true;
		}
	}
}
