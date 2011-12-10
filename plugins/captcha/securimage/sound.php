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

		$captcha = new JCaptchaSecurimage(array(
			'namespace' => $this->input->get('namespace', '_default')
		));

		$params = $params->toArray();

		$captcha->setProperties($params);

		$this->setBody($captcha->getAudibleCode());
	}

	protected function respond()
	{
		$audio = $this->getBody();

		$this->setHeader('Content-Type', 'audio/x-wav');
		$this->setHeader('Content-Disposition', 'attachment; filename="securimage_audio.wav"');
		$this->setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		$this->setHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT', true);
		$this->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate', false);
		$this->setHeader('Pragma', 'no-cache');
		$this->setHeader('Content-Length', strlen($audio));

		$this->sendHeaders();

        echo $audio;
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