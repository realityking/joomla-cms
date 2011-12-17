<?php
/**
 * @package     Joomla
 * @subpackage  Plugin
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Securimage Captcha Plugin.
 * Based on the Securimage Captcha library( http://www.phpcaptcha.org/ )
 *
 * @package		Joomla
 * @subpackage	Plugin
 * @since		2.5
 */
class plgCaptchaSecurimage extends JPlugin
{
	/**
	 * Captcha namespace
	 *
	 * @var	String
	 */
	private $namespace = '_default';

	/**
	 * Constructor
	 *
	 * @param object  $subject  The object to observe
	 * @param array   $config   An optional associative array of configuration settings.
	 * @param array   $options  An optional associative array of options settings.
	 */
	public function __construct($subject, $config, $options = array())
	{
		parent::__construct($subject, $config, $options);

		if (isset($options['namespace'])) {
			$this->namespace = $options['namespace'];
		}

		$this->loadLanguage();
	}

	/**
	 * Initialise the captcha
	 *
	 * @return Boolean
	 */
	public function onInit()
	{
		return true;
	}

	/**
	 * Gets the challenge HTML.
	 *
	 * @return string  The HTML to be embedded in the form.
	 */
	public function onDisplay($name, $id, $class)
	{
		JHtml::_('script', 'securimage/captcha.js', true, true);
		JHtml::_('stylesheet', 'securimage/captcha.css', array(), true);
		$html[] = '<img src="index.php?option=com_media&task=captcha.image&format=raw&namespace='.$this->namespace.'"';
		$html[] = ' alt="captcha" class="securimage-captcha">';
		$html[] = '<div class="securimage-container">';
		$html[] = '<div class="securimage-reload" title="'.JText::_('PLG_SECURIMAGE_TITLE_RELOAD').'"></div>';
		$html[] = '</div>';
		$html[] = '<div style="clear:both"></div>';
		$html[] = '<input type="text" name="'.$name.'" id="'.$id.'" '.$class.' />';

		return implode('', $html);
	}

	/**
	 * Check the Answer
	 *
	 * @return Boolean
	 */
	public function onCheckAnswer($input)
	{
		// Special treatement for this param
		$captcha = new JCaptchaSecurimage(array(
			'namespace' => $this->namespace
		));
		$captcha->case_sensitive = $this->params->get('case_sensitive', false);

		if ($captcha->validate($input))
		{
			return true;
		}
		else
		{
			$this->_subject->setError(JText::_('PLG_SECURIMAGE_ERROR_INCORRECT_CAPTCHA_SOL'));
			return false;
		}
	}
}
