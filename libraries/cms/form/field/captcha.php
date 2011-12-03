<?php
/**
 * @version		$Id: captcha.php
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Framework
 * @subpackage	Form
 * @since		1.6
 */
class JFormFieldCaptcha extends JFormField
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Captcha';

	/**
	 * Method to get the field input.
	 *
	 * @return	string		The field input.
	 */
	protected function getInput()
	{
		$class = $this->element['class'] ? (string) $this->element['class'] : '';
		$plugin = $this->element['plugin'] ? (string) $this->element['plugin'] : '';
		$namespace = $this->element['namespace'] ? (string) $this->element['namespace'] : $this->form->getName();

		if ($plugin === 0 || $plugin === '0'){// Use 0 for none
			return '';
		}
		else{
			if (($captcha = JFactory::getCaptcha($plugin, array('namespace' => $namespace))) == null){
				return '';
			}
		}

		return $captcha->display($this->name, $this->id, $class);
	}
}