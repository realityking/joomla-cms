<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Joomla! Application class
 *
 * The JApplicationCms is a transitional class used to move the Joomla! CMS from using the
 * legacy JApplication class as the root for its application instance to enabling the CMS
 * to build using JApplicationWeb.
 *
 * @package     Joomla.Libraries
 * @subpackage  Application
 * @since       3.0
 */
class JApplicationCms extends JApplicationWeb
{
	/**
	 * The scope of the application.
	 *
	 * @var    string
	 * @since  3.0
	 */
	public $scope = null;

	/**
	 * The client identifier.
	 *
	 * @var    integer
	 * @since  3.0
	 */
	protected $_clientId = null;

	/**
	 * The application message queue.
	 *
	 * @var    array
	 * @since  3.0
	 */
	protected $_messageQueue = array();

	/**
	 * The name of the application.
	 *
	 * @var    array
	 * @since  3.0
	 */
	protected $_name = null;

	/**
	 * The profiler instance
	 *
	 * @var    JProfiler
	 * @since  3.0
	 */
	protected $profiler = null;

	/**
	 * Currently active template
	 *
	 * @var    object
	 * @since  3.0
	 */
	protected $template = null;

	/**
	 * Class constructor.
	 *
	 * @param   mixed  $input   An optional argument to provide dependency injection for the application's
	 *                          input object.  If the argument is a JInput object that object will become
	 *                          the application's input object, otherwise a default input object is created.
	 * @param   mixed  $config  An optional argument to provide dependency injection for the application's
	 *                          config object.  If the argument is a JRegistry object that object will become
	 *                          the application's config object, otherwise a default config object is created.
	 * @param   mixed  $client  An optional argument to provide dependency injection for the application's
	 *                          client object.  If the argument is a JApplicationWebClient object that object will become
	 *                          the application's client object, otherwise a default client object is created.
	 *
	 * @since   3.0
	 */
	public function __construct(JInput $input = null, JRegistry $config = null, JApplicationWebClient $client = null)
	{
		parent::__construct();

		// Load and set the dispatcher
		$this->loadDispatcher();

		// If JDEBUG is defined, load the profiler instance
		if (defined('JDEBUG') && JDEBUG)
		{
			$this->profiler = JProfiler::getInstance('Application');
		}

		// Enable sessions by default.
		if (is_null($this->config->get('session')))
		{
			$this->config->set('session', true);
		}

		// Set the session default name.
		if (is_null($this->config->get('session_name')))
		{
			$this->config->set('session_name', $this->_name);
		}

		// Create the session if a session name is passed.
		if ($this->config->get('session') !== false)
		{
			$this->loadSession();

			// Register the session with JFactory
			JFactory::$session = $this->getSession();
		}

		// Register the application to JFactory
		JFactory::$application = $this;
	}

	/**
	 * Checks the user session.
	 *
	 * If the session record doesn't exist, initialise it.
	 * If session is new, create session variables
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function checkSession()
	{
		$db = JFactory::getDBO();
		$session = JFactory::getSession();
		$user = JFactory::getUser();

		$query = $db->getQuery(true);
		$query->select($query->qn('session_id'))
		->from($query->qn('#__session'))
		->where($query->qn('session_id') . ' = ' . $query->q($session->getId()));

		$db->setQuery($query, 0, 1);
		$exists = $db->loadResult();

		// If the session record doesn't exist initialise it.
		if (!$exists)
		{
			$query->clear();
			if ($session->isNew())
			{
				$query->insert($query->qn('#__session'))
				->columns($query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('time'))
				->values($query->q($session->getId()) . ', ' . (int) $this->getClientId() . ', ' . $query->q((int) time()));
				$db->setQuery($query);
			}
			else
			{
				$query->insert($query->qn('#__session'))
				->columns(
					$query->qn('session_id') . ', ' . $query->qn('client_id') . ', ' . $query->qn('guest') . ', ' .
					$query->qn('time') . ', ' . $query->qn('userid') . ', ' . $query->qn('username')
				)
				->values(
					$query->q($session->getId()) . ', ' . (int) $this->getClientId() . ', ' . (int) $user->get('guest') . ', ' .
					$query->q((int) $session->get('session.timer.start')) . ', ' . (int) $user->get('id') . ', ' . $query->q($user->get('username'))
				);

				$db->setQuery($query);
			}

			// If the insert failed, exit the application.
			try
			{
				$db->execute();
			}
			catch (RuntimeException $e)
			{
				$this->close($e->getCode());
			}

			// Session doesn't exist yet, so create session variables
			if ($session->isNew())
			{
				$session->set('registry', new JRegistry('session'));
				$session->set('user', new JUser);
			}
		}
	}

	/**
	 * Allows the application to load a custom or default session.
	 *
	 * The logic and options for creating this object are adequately generic for default cases
	 * but for many applications it will make sense to override this method and create a session,
	 * if required, based on more specific needs.
	 *
	 * @param   JSession  $session  An optional session object. If omitted, the session is created.
	 *
	 * @return  JApplicationWeb This method is chainable.
	 *
	 * @since   3.0
	 */
	public function loadSession(JSession $session = null)
	{
		if ($session !== null)
		{
			$this->session = $session;

			return $this;
		}

		$options = array();
		$options['name'] = JApplication::getHash($this->config->get('session_name'));

		switch ($this->_clientId)
		{
			case 0:
				if ($this->getCfg('force_ssl') == 2)
				{
					$options['force_ssl'] = true;
				}
				break;

			case 1:
				if ($this->getCfg('force_ssl') >= 1)
				{
					$options['force_ssl'] = true;
				}
				break;
		}

		$session = JFactory::getSession($options);
		$session->initialise($this->input);
		$session->start();

		// TODO: At some point we need to get away from having session data always in the db.

		$db = JFactory::getDBO();

		// Remove expired sessions from the database.
		$time = time();
		if ($time % 2)
		{
			// The modulus introduces a little entropy, making the flushing less accurate
			// but fires the query less than half the time.
			$query = $db->getQuery(true);
			$query->delete($query->qn('#__session'))
				->where($query->qn('time') . ' < ' . $query->q((int) ($time - $session->getExpire())));

			$db->setQuery($query);
			$db->execute();
		}

		// Check to see the the session already exists.
		$handler = $this->getCfg('session_handler');
		if (($handler != 'database' && ($time % 2 || $session->isNew()))
			|| ($handler == 'database' && $session->isNew()))
		{
			$this->checkSession();
		}

		// Set the session object.
		$this->session = $session;

		return $this;
	}

	/**
	 * Enqueue a system message.
	 *
	 * @param   string  $msg   The message to enqueue.
	 * @param   string  $type  The message type. Default is message.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function enqueueMessage($msg, $type = 'message')
	{
		// For empty queue, if messages exists in the session, enqueue them first.
		if (!count($this->_messageQueue))
		{
			$session = JFactory::getSession();
			$sessionQueue = $session->get('application.queue');

			if (count($sessionQueue))
			{
				$this->_messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}

		// Enqueue the message.
		$this->_messageQueue[] = array('message' => $msg, 'type' => strtolower($type));
	}

	/**
	 * Gets a configuration value.
	 *
	 * @param   string  $varname  The name of the value to get.
	 * @param   string  $default  Default value to return
	 *
	 * @return  mixed  The user state.
	 *
	 * @since   3.0
	 */
	public function getCfg($varname, $default = null)
	{
		return $this->config->get('' . $varname, $default);
	}

	/**
	 * Gets the client id of the current running application.
	 *
	 * @return  integer  A client identifier.
	 *
	 * @since   3.0
	 */
	public function getClientId()
	{
		return $this->_clientId;
	}

	/**
	 * Returns the application JMenu object.
	 *
	 * @param   string  $name     The name of the application/client.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  JMenu
	 *
	 * @since   3.0
	 */
	public function getMenu($name = null, $options = array())
	{
		if (!isset($name))
		{
			$name = $this->_name;
		}

		try
		{
			$menu = JMenu::getInstance($name, $options);
		}
		catch (Exception $e)
		{
			return null;
		}

		return $menu;
	}

	/**
	 * Get the system message queue.
	 *
	 * @return  array  The system message queue.
	 *
	 * @since   3.0
	 */
	public function getMessageQueue()
	{
		// For empty queue, if messages exists in the session, enqueue them.
		if (!count($this->_messageQueue))
		{
			$session = JFactory::getSession();
			$sessionQueue = $session->get('application.queue');

			if (count($sessionQueue))
			{
				$this->_messageQueue = $sessionQueue;
				$session->set('application.queue', null);
			}
		}

		return $this->_messageQueue;
	}

	/**
	 * Gets the name of the current running application.
	 *
	 * @return  string  The name of the application.
	 *
	 * @since   3.0
	 */
	public function getName()
	{
		return $this->_name;
	}

	/**
	 * Returns the application JPathway object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  JPathway
	 *
	 * @since   3.0
	 */
	public function getPathway($name = null, $options = array())
	{
		if (!isset($name))
		{
			$name = $this->_name;
		}

		try
		{
			$pathway = JPathway::getInstance($name, $options);
		}
		catch (Exception $e)
		{
			return null;
		}

		return $pathway;
	}

	/**
	 * Returns the application JRouter object.
	 *
	 * @param   string  $name     The name of the application.
	 * @param   array   $options  An optional associative array of configuration settings.
	 *
	 * @return  JRouter
	 *
	 * @since   3.0
	 */
	public static function getRouter($name = null, array $options = array())
	{
		if (!isset($name))
		{
			$app = JFactory::getApplication();
			$name = $app->getName();
		}

		jimport('joomla.application.router');

		try
		{
			$router = JRouter::getInstance($name, $options);
		}
		catch (Exception $e)
		{
			return null;
		}

		return $router;
	}

	/**
	 * Gets a user state.
	 *
	 * @param   string  $key      The path of the state.
	 * @param   mixed   $default  Optional default value, returned if the internal value is null.
	 *
	 * @return  mixed  The user state or null.
	 *
	 * @since   3.0
	 */
	public function getUserState($key, $default = null)
	{
		$session = JFactory::getSession();
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->get($key, $default);
		}

		return $default;
	}

	/**
	 * Gets the value of a user state variable.
	 *
	 * @param   string  $key      The key of the user state variable.
	 * @param   string  $request  The name of the variable passed in a request.
	 * @param   string  $default  The default value for the variable if not found. Optional.
	 * @param   string  $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
	 *
	 * @return  object  The request user state.
	 *
	 * @since   3.0
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
	{
		$cur_state = $this->getUserState($key, $default);
		$new_state = $this->input->get($request, null, $type);

		// Save the new value only if it was set in this request.
		if ($new_state !== null)
		{
			$this->setUserState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}

	/**
	 * Initialise the application.
	 *
	 * @param   array  $options  An optional associative array of configuration settings.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function initialiseApp($options = array())
	{
		// Set the configuration in the API.
		$this->config = JFactory::getConfig();

		// Check that we were given a language in the array (since by default may be blank).
		if (isset($options['language']))
		{
			$this->config->set('language', $options['language']);
		}

		// Set user specific editor.
		$user = JFactory::getUser();
		$editor = $user->getParam('editor', $this->getCfg('editor'));
		if (!JPluginHelper::isEnabled('editors', $editor))
		{
			$editor = $this->getCfg('editor');
			if (!JPluginHelper::isEnabled('editors', $editor))
			{
				$editor = 'none';
			}
		}

		$this->config->set('editor', $editor);

		// Trigger the onAfterInitialise event.
		JPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterInitialise');
	}

	/**
	 * Is admin interface?
	 *
	 * @return  boolean  True if this application is administrator.
	 *
	 * @since   3.0
	 */
	public function isAdmin()
	{
		return ($this->_clientId == 1);
	}

	/**
	 * Is site interface?
	 *
	 * @return  boolean  True if this application is site.
	 *
	 * @since   3.0
	 */
	public function isSite()
	{
		return ($this->_clientId == 0);
	}

	/**
	 * Login authentication function.
	 *
	 * Username and encoded password are passed the onUserLogin event which
	 * is responsible for the user validation. A successful validation updates
	 * the current session record with the user's details.
	 *
	 * Username and encoded password are sent as credentials (along with other
	 * possibilities) to each observer (authentication plugin) for user
	 * validation.  Successful validation will update the current session with
	 * the user details.
	 *
	 * @param   array  $credentials  Array('username' => string, 'password' => string)
	 * @param   array  $options      Array('remember' => boolean)
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   3.0
	 */
	public function login($credentials, $options = array())
	{
		// Get the global JAuthentication object.
		jimport('joomla.user.authentication');

		$authenticate = JAuthentication::getInstance();
		$response = $authenticate->authenticate($credentials, $options);

		if ($response->status === JAuthentication::STATUS_SUCCESS)
		{
			// Validate that the user should be able to login (different to being authenticated).
			// This permits authentication plugins blocking the user
			$authorisations = $authenticate->authorise($response, $options);
			foreach ($authorisations as $authorisation)
			{
				$denied_states = array(JAuthentication::STATUS_EXPIRED, JAuthentication::STATUS_DENIED);
				if (in_array($authorisation->status, $denied_states))
				{
					// Trigger onUserAuthorisationFailure Event.
					$this->triggerEvent('onUserAuthorisationFailure', array((array) $authorisation));

					// If silent is set, just return false.
					if (isset($options['silent']) && $options['silent'])
					{
						return false;
					}

					// Return the error.
					switch ($authorisation->status)
					{
						case JAuthentication::STATUS_EXPIRED:
							return JError::raiseWarning('102002', JText::_('JLIB_LOGIN_EXPIRED'));
							break;
						case JAuthentication::STATUS_DENIED:
							return JError::raiseWarning('102003', JText::_('JLIB_LOGIN_DENIED'));
							break;
						default:
							return JError::raiseWarning('102004', JText::_('JLIB_LOGIN_AUTHORISATION'));
							break;
					}
				}
			}

			// Import the user plugin group.
			JPluginHelper::importPlugin('user');

			// OK, the credentials are authenticated and user is authorised.  Lets fire the onLogin event.
			$results = $this->triggerEvent('onUserLogin', array((array) $response, $options));

			/*
			 * If any of the user plugins did not successfully complete the login routine
			 * then the whole method fails.
			 *
			 * Any errors raised should be done in the plugin as this provides the ability
			 * to provide much more information about why the routine may have failed.
			 */

			if (!in_array(false, $results, true))
			{
				// Set the remember me cookie if enabled.
				if (isset($options['remember']) && $options['remember'])
				{
					// Create the encryption key, apply extra hardening using the user agent string.
					$privateKey = JApplication::getHash(@$_SERVER['HTTP_USER_AGENT']);

					$key = new JCryptKey('simple', $privateKey, $privateKey);
					$crypt = new JCrypt(new JCryptCipherSimple, $key);
					$rcookie = $crypt->encrypt(serialize($credentials));
					$lifetime = time() + 365 * 24 * 60 * 60;

					// Use domain and path set in config for cookie if it exists.
					$cookie_domain = $this->getCfg('cookie_domain', '');
					$cookie_path = $this->getCfg('cookie_path', '/');
					setcookie(JApplication::getHash('JLOGIN_REMEMBER'), $rcookie, $lifetime, $cookie_path, $cookie_domain);
				}

				return true;
			}
		}

		// Trigger onUserLoginFailure Event.
		$this->triggerEvent('onUserLoginFailure', array((array) $response));

		// If silent is set, just return false.
		if (isset($options['silent']) && $options['silent'])
		{
			return false;
		}

		// If status is success, any error will have been raised by the user plugin
		if ($response->status !== JAuthentication::STATUS_SUCCESS)
		{
			JLog::add($response->error_message, JLog::WARNING, 'jerror');
		}

		return false;
	}

	/**
	 * Logout authentication function.
	 *
	 * Passed the current user information to the onUserLogout event and reverts the current
	 * session record back to 'anonymous' parameters.
	 * If any of the authentication plugins did not successfully complete
	 * the logout routine then the whole method fails. Any errors raised
	 * should be done in the plugin as this provides the ability to give
	 * much more information about why the routine may have failed.
	 *
	 * @param   integer  $userid   The user to load - Can be an integer or string - If string, it is converted to ID automatically
	 * @param   array    $options  Array('clientid' => array of client id's)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   3.0
	 */
	public function logout($userid = null, $options = array())
	{
		// Get a user object from the JApplication.
		$user = JFactory::getUser($userid);

		// Build the credentials array.
		$parameters['username'] = $user->get('username');
		$parameters['id'] = $user->get('id');

		// Set clientid in the options array if it hasn't been set already.
		if (!isset($options['clientid']))
		{
			$options['clientid'] = $this->getClientId();
		}

		// Import the user plugin group.
		JPluginHelper::importPlugin('user');

		// OK, the credentials are built. Lets fire the onLogout event.
		$results = $this->triggerEvent('onUserLogout', array($parameters, $options));

		// Check if any of the plugins failed. If none did, success.

		if (!in_array(false, $results, true))
		{
			// Use domain and path set in config for cookie if it exists.
			$cookie_domain = $this->getCfg('cookie_domain', '');
			$cookie_path = $this->getCfg('cookie_path', '/');
			setcookie(JApplication::getHash('JLOGIN_REMEMBER'), false, time() - 86400, $cookie_path, $cookie_domain);

			return true;
		}

		// Trigger onUserLoginFailure Event.
		$this->triggerEvent('onUserLogoutFailure', array($parameters));

		return false;
	}

	/**
	 * Route the application.
	 *
	 * Routing is the process of examining the request environment to determine which
	 * component should receive the request. The component optional parameters
	 * are then set in the request object to be processed when the application is being
	 * dispatched.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	protected function route()
	{
		// Get the full request URI.
		$uri = clone JURI::getInstance();

		$router = $this->getRouter();
		$result = $router->parse($uri);

		foreach ($result as $key => $value)
		{
			$this->input->def($key, $value);
		}

		// Trigger the onAfterRoute event.
		JPluginHelper::importPlugin('system');
		$this->triggerEvent('onAfterRoute');
	}

	/**
	 * Sets the value of a user state variable.
	 *
	 * @param   string  $key    The path of the state.
	 * @param   string  $value  The value of the variable.
	 *
	 * @return  mixed  The previous state, if one existed.
	 *
	 * @since   3.0
	 */
	public function setUserState($key, $value)
	{
		$session = JFactory::getSession();
		$registry = $session->get('registry');

		if (!is_null($registry))
		{
			return $registry->set($key, $value);
		}

		return null;
	}
}
