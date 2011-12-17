<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Captcha Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @since		2.5
 */
class MediaControllerCaptcha extends JController
{
	/**
	 * Output image to the browser
	 *
	 * @since 2.5
	 */
	public function image()
	{
		$document = JFactory::getDocument();
		$plugin = JPluginHelper::getPlugin('captcha', 'securimage');
		$params = new JRegistry($plugin->params);

		switch ($params->get('image_type', 'png'))
		{
			case 'jpg':
				$document->setMimeEncoding('image/jpeg');
				break;
			case 'gif':
				$document->setMimeEncoding('image/gif');
				break;
			case 'png':
			default:
				$document->setMimeEncoding('image/png');
				break;
		}

		$captcha = new JCaptchaSecurimage(array(
			'namespace' => JRequest::getCmd('namespace', '_default')
		));

		$params = $params->toArray();
		$params['bgimg'] = (array) $params['bgimg'];
		if (count($params['bgimg']) == 1 && $params['bgimg'][0] == -1){
			$params['bgimg'] = false;
		} elseif (($k = array_search(-1, $params['bgimg'])) !== false) {
			unset($params['bgimg'][$k]);
		}

		$captcha->setProperties($params);

		echo $captcha->create();

		$document->setModifiedDate(gmdate('D, d M Y H:i:s') . ' GMT');
		$document->setCharset(null);
		JResponse::allowCache(true);
		JResponse::setHeader('Expires', 'Mon, 1 Jan 2001 00:00:00 GMT', true);
		JResponse::setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0', true);
		JResponse::setHeader('Pragma', 'no-cache', true);
	}
}
