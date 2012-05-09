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
 * Default controller class for the Joomla Installer.
 *
 * @package     Joomla.Installation
 * @subpackage  Controller
 * @since       3.0
 */
class InstallationControllerDefault extends JControllerBase
{
	/**
	 * Execute the controller.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.0
	 */
	public function execute()
	{
		// Get the application
		$app = $this->getApplication();

		// Get the document object.
		$document = $app->getDocument();

		// Set the default view name and format from the Request.
		if (file_exists(JPATH_CONFIGURATION . '/configuration.php') && (filesize(JPATH_CONFIGURATION . '/configuration.php') > 10) && file_exists(JPATH_INSTALLATION . '/index.php'))
		{
			$default_view = 'remove';
		}
		else
		{
			$default_view = 'language';
		}

		$vName = $app->input->getWord('view', $default_view);
		$vFormat = $document->getType();
		$lName = $app->input->getWord('layout', 'default');

		if (strcmp($vName, $default_view) == 0)
		{
			$app->input->set('view', $default_view);
		}

		// Register the layout paths for the view
		$paths = new SplPriorityQueue;
		$paths->insert(JPATH_INSTALLATION . '/view/' . $vName . '/tmpl', 'normal');

		$vClass = 'InstallationView' . ucfirst($vName) . ucfirst($vFormat);
		$view = new $vClass(new InstallationModelSetup, $paths);
		$view->setLayout($lName);

		// Render our view and return it to the application.
		return $view->render();
	}
}
