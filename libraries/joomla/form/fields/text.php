<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a one line text field.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @link        http://www.w3.org/TR/html-markup/input.text.html#input.text
 * @since       11.1
 */
class JFormFieldText extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 *
	 * @since  11.1
	 */
	protected $type = 'Text';

	/**
	 * Value of the type attribute of the rendered input element.
	 *
	 * @var    string
	 *
	 * @since  12.2
	 */
	protected $inputtype = 'text';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$size        = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$maxLength   = $this->element['maxlength'] ? ' maxlength="' . (int) $this->element['maxlength'] . '"' : '';
		$class       = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$placeholder = $this->element['placeholder'] ? ' placeholder="' . (string) $this->element['placeholder'] . '"' : '';
		$required    = $this->required ? ' required="required"' : '';
		$readonly    = ((string) $this->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled    = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		return '<input type="' . $this->inputtype . '" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled
			. $readonly . $required . $placeholder . $onchange . $maxLength . '/>';
	}
}
