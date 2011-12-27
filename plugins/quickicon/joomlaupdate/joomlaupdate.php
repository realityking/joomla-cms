<?php
/**
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Joomla! udpate notification plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Quickicon.Joomla
 * @since		2.5
 */
class plgQuickiconJoomlaupdate extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 *
	 * @since       2.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}
	
	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 * 
	 * @return array An icon definition associative array, consisting of the
	 *				 keys link, image, text and access.
	 *
	 * @since       2.5
	 */
	public function onGetIcon()
	{
		$cur_template = JFactory::getApplication()->getTemplate();
		$document = JFactory::getDocument();
		$document->addScript(JURI::base().'../media/updater.js');
		$document->addScriptDeclaration("
			window.addEvent('domready', function(){
				new Joomla.UpdateNotification({
					id: 'plg_quickicon_joomlaupdate',
					url: '" . JURI::base().'index.php?option=com_installer&format=json&task=update.notification' . "',
					updateFoundString: '" . JText::_('PLG_QUICKICON_JOOMLAUPDATE_UPDATEFOUND') . "',
					upToDateString: '" . JText::_('PLG_QUICKICON_JOOMLAUPDATE_UPTODATE') . "',
					errorString: '" . JText::_('PLG_QUICKICON_JOOMLAUPDATE_ERROR') . "',
					updateFoundImg: '" . JURI::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-jupdate-updatefound.png' . "',
					upToDateImg: '" . JURI::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-jupdate-uptodate.png' . "',
					errorImg: '" . JURI::base(true) .'/templates/'. $cur_template .'/images/header/icon-48-deny.png' . "'
				});
			});
		");
		
		return array(
			'link' => 'index.php?option=com_installer&view=update',
			'image' => 'header/icon-48-download.png',
			'text' => JText::_('PLG_QUICKICON_JOOMLAUPDATE_CHECKING'),
			'access' => array('core.manage', 'com_installer'),
			'id' => 'plg_quickicon_joomlaupdate'
		);
	}
}