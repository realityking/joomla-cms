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
 * Controller class to set the language for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerSetlanguage extends JControllerBase
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

		// Very crude workaround to give an error message when JSON is disabled
		if (!function_exists('json_encode') || !function_exists('json_decode'))
		{
			$app->setHeader('status', 500);
			$app->setHeader('Content-Type', 'application/json; charset=utf-8');
			$app->sendHeaders();
			echo '{"token":"' . JSession::getFormToken(true) . '","lang":"' . JFactory::getLanguage()->getTag() . '","error":true,"header":"' . JText::_('INSTL_HEADER_ERROR') . '","message":"' . JText::_('INSTL_WARNJSON') . '"}';
			$app->close();
		}

		// Check for potentially unwritable session
		$session = JFactory::getSession();

		if ($session->isNew())
		{
			$app->sendJsonResponse(new Exception(JText::_('INSTL_COOKIES_NOT_ENABLED'), 500));
		}

		// Get the setup model.
		$model = new InstallationModelSetup;

		// Get the posted values from the request and validate them.
		$data = $app->input->post->get('jform', array(), 'array');

		$return = $model->validate($data, 'language');

		$r = new JObject;

		// Check for validation errors.
		if ($return === false)
		{
			/*
			 * The validate method enqueued all messages for us, so we just need to
			 * redirect back to the language selection screen.
			 */
			$r->view = 'language';
			$app->sendJsonResponse($r);
			return false;
		}

		// Store the options in the session.
		$model->storeOptions($return);

		// Redirect to the next page.
		$r->view = 'preinstall';
		$app->sendJsonResponse($r);
		return true;
	}
}
