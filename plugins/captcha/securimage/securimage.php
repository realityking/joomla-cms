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
	 * Captcha Plugin object
	 *
	 * @var	JCaptchaSecurimage
	 */
	private $captcha;

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

		$namespace = isset($options['namespace']) ? $options['namespace'] : '_default';
		$this->captcha = new JCaptchaSecurimage(array('namespace' => $namespace));
	}

	/**
	 * Initialise the captcha
	 *
	 * @return Boolean
	 */
	public function onInit()
	{
		$params = $this->params->toArray();

		if (is_array($params['bgimg']) && count($params['bgimg']) == 1 && $params['bgimg'][0] == -1){
			$params['bgimg'] = false;
		} elseif (($k = array_search(-1, $params['bgimg'])) !== false) {
			unset($params['bgimg'][$k]);
		}

		if ($this->captcha->setProperties($params)) return true;
	}

	/**
	 * Gets the challenge HTML.
	 *
	 * @return string  The HTML to be embedded in the form.
	 */
	public function onDisplay($name, $id, $class)
	{
		// Try create the captcha
		if (!$this->captcha->create())
		{
			$this->_subject->setError($this->captcha->getError());
			return false;
		}

		$html[] = '<img src="'.$this->captcha->fileUri.'" alt="captcha"><br />';
		$html[] = '<input type="text" name="'.$name.'[code]" id="'.$id.'" '.$class.' />';
		$html[] = '<input type="hidden" name="'.$name.'[id]" value="'.$this->captcha->id.'" />';

		return implode("\n", $html);
	}

	/**
	 * Check the Answer
	 *
	 * @return Boolean
	 */
	public function onCheckAnswer($input)
	{
		// Special treatement for this param
		$this->captcha->case_sensitive = $this->params->get('case_sensitive', false);

		if ($this->captcha->validate($input->id, $input->code))
		{
  			return true;
  		}
  		else
  		{
  			$this->loadLanguage();
  			$this->_subject->setError(JText::_('PLG_SECURIMAGE_ERROR_INCORRECT_CAPTCHA_SOL'));
  			return false;
  		}
	}
}
