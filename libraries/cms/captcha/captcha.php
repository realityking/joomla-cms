<?php
/**
 * @package		Joomla.Libraries
 * @subpackage	Captcha
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.file');
jimport('joomla.base.observable');

/**
 * Joomla! Captcha base object
 *
 * @abstract
 * @package		Joomla.Libraries
 * @subpackage	Captcha
 * @since		2.5
 */
class JCaptcha extends JObservable
{
	/**
	 * Captcha Plugin object
	 *
	 * @var	object
	 */
	private $_captcha;

	/**
	 * Editor Plugin name
	 *
	 * @var string
	 */
	private $_name;
	
	/**
	 * Captcha Plugin object
	 *
	 * @var	array
	 */
	private static $_instances = array();

	/**
	 * Class constructor.
	 *
	 * @param	string	$editor  The editor to use.
	 * @param	array	$options  Associative array of options.
	 *
	 * @since 2.5
	 */
	public function __construct($captcha, $options)
	{
		$this->_name = $captcha;
		$this->_load($options);
	}

	/**
	 * Returns the global Captcha object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param	string	$editor  The editor to use.
	 * @param	array	$options  Associative array of options.
	 *
	 * @return	object	The JCaptcha object.
	 *
	 * @since 2.5
	 */
	public static function getInstance($captcha = '', array $options = array())
	{
		$captcha = empty($captcha) ? JFactory::getConfig()->get('captcha') : $captcha;
		$signature = md5(serialize(array($captcha, $options)));

		if (empty(self::$_instances[$signature]))
		{
			try
			{
				self::$_instances[$signature] = new JCaptcha($captcha, $options);
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return null;
			}
		}

		return self::$_instances[$signature];
	}

	/**
	 * @return boolean True on success
	 *
	 * @since	2.5
	 */
	public function initialise($id)
	{
		$args['id']		= $id ;
		$args['event']	= 'onInit';

		try
		{
			$this->_captcha->update($args);
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			return false;
		}

		return true;
	}

	/**
	 * Get the HTML for the captcha.
	 *
	 * @return 	the return value of the function "onDisplay" of the selected Plugin.
	 * @since	2.5
	 */
	public function display($name, $id, $class = '')
	{
		// Check if captcha is already loaded.
		if (is_null($this->_captcha)) {
			return;
		}

		// Initialise the Captcha.
		if (!$this->initialise($id)) {
			return;
		}

		$args['name']		= $name;
		$args['id']			= $id ? $id : $name;
		$args['class']		= $class ? 'class="'.$class.'"' : '';
		$args['event']		= 'onDisplay';

		return $this->_captcha->update($args);
	}

	/**
	 * Checks if the answer is correct.
	 *
	 * @return 	the return value of the function "onCheckAnswer" of the selected Plugin.
	 * @since	2.5
	 */
	public function checkAnswer($code)
	{
		//check if captcha is already loaded
		if (is_null(($this->_captcha))) {
			return;
		}

		$args['code'] = $code;
		$args['event'] = 'onCheckAnswer';

		return $this->_captcha->update($args);
	}

	/**
	 * Load the Captcha plug-in.
	 *
	 * @param	array	$options  Associative array of options.
	 *
	 * @return  void
	 *
	 * @since	2.5
	 */
	private function _load(array $options = array())
	{
		// Build the path to the needed captcha plugin
		$name = JFilterInput::getInstance()->clean($this->_name, 'cmd');
		$path = JPATH_PLUGINS . '/captcha/' . $name . '/' . $name . '.php';

		if (!JFile::exists($path))
		{
			$path = JPATH_PLUGINS . '/captcha/' . $name . '.php';
			if (!JFile::exists($path))
			{
				throw new Exception(JText::sprintf('JLIB_CAPTCHA_ERROR_PLUGIN_NOT_FOUND', $name));
			}
		}

		// Require plugin file
		require_once $path;

		// Get the plugin
		$plugin = JPluginHelper::getPlugin('captcha', $this->_name);
		if (!$plugin) throw new Exception(JText::sprintf('JLIB_CAPTCHA_ERROR_LOADING', $name));
		$params = new JRegistry($plugin->params);
		$plugin->params = $params;

		// Build captcha plugin classname
		$name = 'plgCaptcha'.$this->_name;
		$this->_captcha = new $name($this, (array)$plugin, $options);
	}
}