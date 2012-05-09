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
 * Controller class to remove the installation application.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerRemovefolder extends JControllerBase
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

		// Set the path
		$path = JPATH_INSTALLATION;

		// Check whether the folder still exists
		if (!file_exists($path))
		{
			$app->sendJsonResponse(new Exception(JText::sprintf('INSTL_COMPLETE_ERROR_FOLDER_ALREADY_REMOVED'), 500));
		}

		// Check whether we need to use FTP
		$useFTP = false;
		if ((file_exists($path) && !is_writable($path)))
		{
			$useFTP = true;
		}

		// Check for safe mode
		if (ini_get('safe_mode'))
		{
			$useFTP = true;
		}

		// Enable/Disable override
		if (!isset($options->ftpEnable) || ($options->ftpEnable != 1))
		{
			$useFTP = false;
		}

		if ($useFTP == true)
		{
			// Connect the FTP client
			jimport('joomla.filesystem.path');

			$ftp = JClientFtp::getInstance($options->ftp_host, $options->ftp_port);
			$ftp->login($options->ftp_user, $options->ftp_pass);

			// Translate path for the FTP account
			$file = JPath::clean(str_replace(JPATH_CONFIGURATION, $options->ftp_root, $path), '/');
			$return = $ftp->delete($file);

			// Delete the extra XML file while we're at it
			if ($return)
			{
				$file = JPath::clean($options->ftp_root . '/joomla.xml');
				if (file_exists($file))
				{
					$return = $ftp->delete($file);
				}
			}

			$ftp->quit();
		}
		else
		{
			/*
			 * Try to delete the folder.
			 * We use output buffering so that any error message echoed JFolder::delete
			 * doesn't land in our JSON output.
			 */
			ob_start();
			$return = JFolder::delete($path) && (!file_exists(JPATH_ROOT . '/joomla.xml') || JFile::delete(JPATH_ROOT . '/joomla.xml'));
			ob_end_clean();
		}

		// If an error was encountered return an error.
		if (!$return)
		{
			$app->sendJsonResponse(new Exception(JText::_('INSTL_COMPLETE_ERROR_FOLDER_DELETE'), 500));
		}

		// Create a response body.
		$r = new stdClass;
		$r->text = JText::_('INSTL_COMPLETE_FOLDER_REMOVED');

		// Send the response.
		$app->sendJsonResponse($r);
	}
}
