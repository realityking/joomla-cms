<?php
/**
 * @package		Joomla
 * @subpackage	Plugin
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
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
	 * @param	object	$subject	The object to observe
	 * @param	array	$config		An optional associative array of configuration settings.
	 * @param	array	$options	An optional associative array of options settings.
	 */
	public function __construct($subject, $config, $options = array())
	{
		parent::__construct($subject, $config, $options);

		$namespace = !empty($options['namespace']) ? $options['namespace'] : '_default';
		$this->captcha = new JCaptchaSecurimage();
		$this->captcha->set('namespace', $namespace);
	}

	/**
	 * Initialise the captcha
	 *
	 * @return	Boolean
	 */
	public function onInit()
	{
		$params = $this->params->toArray();

		if (isset($params['image_bg_color'])) {
			$params['image_bg_color'] = new JCaptchaColor(trim($params['image_bg_color']));
		}
		if (isset($params['line_color'])) {
			$params['line_color'] = new JCaptchaColor(trim($params['line_color']));
		}

		if (isset($params['text_color']))
		{
			if (strpos($params['text_color'], ','))
			{
				$multi_text_color = explode(',', $params['text_color']);
				foreach ($multi_text_color as $color) {
					$colors[] = new JCaptchaColor(trim($color));
				}
				$params['text_color'] = $colors;
			}
			else {
				$params['text_color'] = new JCaptchaColor(trim($params['text_color']));
			}
		}

		// Exclude empty values to JCapcthaHelper use the default values.
		foreach($params as $k => $v) {
			if($v === '') unset($params[$k]);
		}

		if($this->captcha->setProperties($params)) return true;
	}
	/**
	 * Gets the challenge HTML.
	 *
	 * @return	string	The HTML to be embedded in the form.
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
	 * @return	Boolean
	 */
	public function onCheckAnswer($input)
	{
		// Special treatement for this param
		$this->captcha->set('case_sensitive', $this->params->get('case_sensitive', false));

		if($this->captcha->validate($input->id, $input->code)) {
  			return true;
  		}
  		else {
  			$this->_subject->setError($this->captcha->getError());
  			return false;
  		}
	}
}
