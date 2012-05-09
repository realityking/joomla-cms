<?php
/**
 * @package     Joomla.Installation
 * @subpackage  View
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * The Installation Complete View
 *
 * @package     Joomla.Installation
 * @subpackage  View
 * @since       3.0
 */
class InstallationViewCompleteHtml extends JViewHtml
{
	/**
	 * Redefine the model so the correct type hinting is available.
	 *
	 * @var     InstallationModelSetup
	 * @since   3.0
	 */
	protected $model;

	/**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   3.0
	 * @throws  RuntimeException
	 */
	public function render()
	{
		$app = JFactory::getApplication();

		// Register the document
		$this->document = $app->getDocument();

		// Get the options
		$this->options = $this->model->getOptions();

		// Get the config string from the session.
		$this->config = JFactory::getSession()->get('setup.config', null);

		return parent::render();
	}
}
