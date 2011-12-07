<?php

define('_JEXEC', 1);
define('JPATH_BASE', realpath(dirname(__FILE__).'/../../../'));

require_once JPATH_BASE.'/libraries/import.php';
require_once JPATH_BASE.'/includes/defines.php';
require_once JPATH_BASE.'/includes/framework.php';

jimport('joomla.application.component.helper');
jimport('joomla.application.web');

class Captcha extends JWeb
{
	public function __construct($input = null, $thisig = null, $client = null)
	{
		parent::__construct($input, $thisig, $client);
		$this->initialise(null, false);
	}

	protected function doExecute()
	{
		$plugin = JPluginHelper::getPlugin('captcha', 'securimage');
		$params = new JRegistry($plugin->params);

		switch ($params->get('image_type', 'png'))
		{
			case 'jpg':
				$this->mimeType = 'image/jpeg';
				break;
			case 'gif':
				$this->mimeType = 'image/gif';
				break;
			case 'png':
			default:
				$this->mimeType = 'image/png';
				break;
		}

		$captcha = new JCaptchaSecurimage(array(
			'namespace' => $this->input->get('namespace', '_default')
		));

		$params = $params->toArray();
		$params['bgimg'] = (array) $params['bgimg'];
		if (count($params['bgimg']) == 1 && $params['bgimg'][0] == -1){
			$params['bgimg'] = false;
		} elseif (($k = array_search(-1, $params['bgimg'])) !== false) {
			unset($params['bgimg'][$k]);
		}

		$captcha->setProperties($params);

		$this->setBody($captcha->create());
	}

	protected function respond()
	{
		// Send the content-type header.
		$this->setHeader('Content-Type', $this->mimeType);

		// Expires in the past.
		$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		// Always modified.
		$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
		$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', false);
		// HTTP 1.0
		$this->setHeader('Pragma', 'no-cache');

		$this->sendHeaders();

		echo $this->getBody();
	}

	protected function loadSession()
	{
		$options['name'] = JUtility::getHash('site');

		if ($this->get('force_ssl') == 2)
		{
			$options['force_ssl'] = true;
		}


		// Get the editor configuration setting
		$handler = $this->get('session_handler', 'none');

		// Config time is in minutes
		$options['expire'] = ($this->get('lifetime')) ? $this->get('lifetime') * 60 : 900;

		$this->session = JSession::getInstance($handler, $options);
	}
}

JWeb::getInstance('Captcha')->execute();