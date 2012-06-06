<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * JPlugin Class
 *
 * @package     Joomla.Platform
 * @subpackage  Plugin
 * @since       11.1
 */
abstract class JPlugin extends JObject implements JEventSubscriber
{
	/**
	 * A JRegistry object holding the parameters for the plugin
	 *
	 * @var    JRegistry
	 * @since  11.1
	 */
	public $params = null;

	/**
	 * The name of the plugin
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_name = null;

	/**
	 * The plugin type
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_type = null;

	/**
	 * Event object to observe.
	 *
	 * @var    object
	 * @since  12.2
	 */
	protected $_subject = null;

	/**
	 * Constructor
	 *
	 * @param   object  $subject  The object to observe
	 * @param   array   $config   An optional associative array of configuration settings.
	 *                            Recognized key values include 'name', 'group', 'params', 'language'
	 *                            (this list is not meant to be comprehensive).
	 *
	 * @since   11.1
	 */
	public function __construct($subject = null, array $config = array())
	{
		// Get the parameters.
		if (isset($config['params']))
		{
			if ($config['params'] instanceof JRegistry)
			{
				$this->params = $config['params'];
			}
			else
			{
				$this->params = new JRegistry;
				$this->params->loadString($config['params']);
			}
		}

		// Get the plugin name.
		if (isset($config['name']))
		{
			$this->_name = $config['name'];
		}

		// Get the plugin type.
		if (isset($config['type']))
		{
			$this->_type = $config['type'];
		}
		
		if ($subject)
		{
			// Register the observer ($this) so we can be notified
			$subject->attach($this);

			// Set the subject to observe
			$this->_subject = $subject;
		}
	}

	/**
	 * Loads the plugin language file
	 *
	 * @param   string  $extension  The extension for which a language file should be loaded
	 * @param   string  $basePath   The basepath to use
	 *
	 * @return  boolean  True, if the file has successfully loaded.
	 *
	 * @since   11.1
	 */
	public function loadLanguage($extension = '', $basePath = JPATH_ADMINISTRATOR)
	{
		if (empty($extension))
		{
			$extension = 'plg_' . $this->_type . '_' . $this->_name;
		}

		$lang = JFactory::getLanguage();
		return $lang->load(strtolower($extension), $basePath, null, false, false)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, null, false, false)
			|| $lang->load(strtolower($extension), $basePath, $lang->getDefault(), false, false)
			|| $lang->load(strtolower($extension), JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name, $lang->getDefault(), false, false);
	}

	/**
	 * Returns an array of event names the subscriber wants to listen to.
	 *
	 * The array is generated based on the methods present in the plugin class.
	 * Magic methods are excluded. Plugins may want to override this.
	 *
	 * @return  array  The events to listen to.
     *
	 * @since   12.2
	 * @see     JEventSubscriber
	 */
	public static function getSubscribedEvents()
	{
		$class = get_called_class();
		$methods = array_diff(get_class_methods($class), get_class_methods('JPlugin'));
		$events = array();
		foreach ($methods as $method)
		{
			// Remove magic methods
			if (strpos($method, '__') === 0)
			{
				continue;
			}
			
			$events[$method] = $method;
		}
		return $events;
	}
	
	/**
	 * Method to trigger events.
	 * The method first generates the even from the argument array. Then it unsets the argument
	 * since the argument has no bearing on the event handler.
	 * If the method exists it is called and returns its return value. If it does not exist it
	 * returns null.
	 *
	 * @param   array  &$args  Arguments
	 *
	 * @return  mixed  Routine return value
	 *
	 * @since   11.1
	 * @deprecated  13.3 
	 */
	public function update(&$args)
	{
		JLog::add('JPlugin::update() has been deprecated and should not be used anymore.', JLog::WARNING, 'deprecated');
		
		// First let's get the event from the argument array. Next we will unset the
		// event argument as it has no bearing on the method to handle the event.
		$event = $args['event'];
		unset($args['event']);

		/*
		 * If the method to handle an event exists, call it and return its return
		 * value.  If it does not exist, return null.
		 */
		if (method_exists($this, $event))
		{
			return call_user_func_array(array($this, $event), $args);
		}
		else
		{
			return null;
		}
	}
}
