<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Application
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.application.component.controller');

/**
 * Controller tailored to suit most form-based admin operations.
 *
 * @package     Joomla.Platform
 * @subpackage  Application
 * @since       11.1
 * @todo        Add ability to set redirect manually to better cope with frontend usage.
 */
class JControllerFormUcm extends JController
{
	/**
	 * The context for storing internal data, e.g. record.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $context;

	/**
	 * Type of content handled.
	 *
	 * @var    string
	 * @since  12.1
	 */
	protected $content_type = '';

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $option;

	/**
	 * The URL view item variable.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $view_item;

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $view_list;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $text_prefix;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   11.1
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the option as com_NameOfController
		if (empty($this->option))
		{
			$this->option = 'com_' . strtolower($this->getName());
		}

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the context as the suffix, eg: OptionControllerContent.
		if (empty($this->context))
		{
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
			{
				JError::raiseError(500, JText::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'));
			}
			$this->context = strtolower($r[2]);
		}

		// Guess the item view as the context.
		if (empty($this->view_item))
		{
			$this->view_item = $this->context;
		}

		// Guess the list view as the plural of the item view.
		if (empty($this->view_list))
		{
			// @TODO Probably worth moving to an inflector class based on
			// http://kuwamoto.org/2007/12/17/improved-pluralizing-in-php-actionscript-and-ror/

			// Simple pluralisation based on public domain snippet by Paul Osman
			// For more complex types, just manually set the variable in your class.
			$plural = array(
				array('/(x|ch|ss|sh)$/i', "$1es"),
				array('/([^aeiouy]|qu)y$/i', "$1ies"),
				array('/([^aeiouy]|qu)ies$/i', "$1y"),
				array('/(bu)s$/i', "$1ses"),
				array('/s$/i', "s"),
				array('/$/', "s"));

			// check for matches using regular expressions
			foreach ($plural as $pattern)
			{
				if (preg_match($pattern[0], $this->view_item))
				{
					$this->view_list = preg_replace($pattern[0], $pattern[1], $this->view_item);
					break;
				}
			}
		}

		// Apply, Save & New, and Save As copy should be standard on forms.
		$this->registerTask('apply', 'save');
		$this->registerTask('save2new', 'save');
		$this->registerTask('save2copy', 'save');
	}

	/**
	 * Method to add a new record.
	 *
	 * @return  mixed  True if the record can be added, a JError object if not.
	 *
	 * @since   11.1
	 */
	public function add()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$context = "$this->option.edit.$this->context";

		// Access check.
		if (!$this->allowAdd())
		{
			// Set the internal error and also the redirect error.
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_CREATE_RECORD_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		$factory = JContentFactory::getInstance();
		$object = $factory->getContent($this->content_type)->create();

		// Clear the record edit information from the session.
		$app->setUserState($context . '.data', null);

		// Redirect to the edit screen.
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($object->content_id, 'id'), false
			)
		);

		return true;
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	protected function allowAdd($data = array())
	{
		$user = JFactory::getUser();
		return ($user->authorise('core.create', $this->option) || count($user->getAuthorisedCategories($this->option, 'core.create')));
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key; default is id.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   11.1
	 */
	protected function allowSave($data, $key = 'id')
	{
		// Initialise variables.
		$recordId = isset($data[$key]) ? $data[$key] : '0';

		if ($recordId)
		{
			return $this->allowEdit($data, $key);
		}
		else
		{
			return $this->allowAdd($data);
		}
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model of the component being processed.
	 *
	 * @return	boolean	 True if successful, false otherwise and internal error is set.
	 *
	 * @since	11.1
	 */
	public function batch($model)
	{
		// Initialise variables.
		$input	= JFactory::getApplication()->input;
		$vars	= $input->post->get('batch', array(), 'array');
		$cid	= $input->post->get('cid', array(), 'array');

		// Build an array of item contexts to check
		$contexts = array();
		foreach ($cid as $id)
		{
			// If we're coming from com_categories, we need to use extension vs. option
			if (isset($this->extension))
			{
				$option = $this->extension;
			}
			else
			{
				$option = $this->option;
			}
			$contexts[$id] = $option . '.' . $this->context . '.' . $id;
		}

		// Attempt to run the batch operation.
		if ($model->batch($vars, $cid, $contexts))
		{
			$this->setMessage(JText::_('JLIB_APPLICATION_SUCCESS_BATCH'));

			return true;
		}
		else
		{
			$this->setMessage(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_FAILED', $model->getError()));
			return false;
		}
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   11.1
	 */
	public function cancel($key = null)
	{
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$factory = JContentFactory::getInstance();
		$model = $this->getModel();
		$table = $model->getTable();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";

		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = JRequest::getInt($key);

		// Attempt to check-in the current record.
		if ($recordId)
		{
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId))
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);

				return false;
			}

			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');

					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $key), false
						)
					);

					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		return true;
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   11.1
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$context = "$this->option.edit.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : JRequest::getInt($urlVar));
		$checkin = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return true;
		}
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since   11.1
	 */
	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		if (empty($name))
		{
			$name = $this->context;
		}

		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   11.1
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$tmpl = JRequest::getCmd('tmpl');
		$layout = JRequest::getCmd('layout', 'edit');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if ($layout)
		{
			$append .= '&layout=' . $layout;
		}

		if ($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   11.1
	 */
	protected function getRedirectToListAppend()
	{
		$tmpl = JRequest::getCmd('tmpl');
		$append = '';

		// Setup redirect info.
		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		return $append;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModel  &$model     The data model object.
	 * @param   array   $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function postSaveHook(JModel &$model, $validData = array())
	{
	}

	/**
	 * Method to save a record.
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   11.1
	 */
	public function save()
	{
		// Check for request forgeries.
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app     = JFactory::getApplication();
		$lang    = JFactory::getLanguage();
		$factory = JContentFactory::getInstance();
		$data    = JRequest::getVar('jform', array(), 'post', 'array');
		$task    = $this->getTask();

		$object = $factory->getContent($this->content_type)->load($data['content_id']);
		$object->checkout();
		$object->bind($data);
		
		if ($object->isTemporary())
		{
			$object->temporary = 0;
		}
		$object->update()->checkin();

		// Redirect the user based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Checkout the article again
				$object->checkout();

				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($object->content_id, 'id'), false
					)
				);
				break;

			case 'save2new':
				//Create a new temporary content object
				$object = $factory->getContent($this->content_type)->create();

				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($object->content_id, 'id'), false
					)
				);
				break;

			default:
				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
				break;
		}
	}
}
