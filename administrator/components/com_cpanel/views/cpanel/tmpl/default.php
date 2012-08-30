<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<div class="row-fluid">
	<div class="span3">
		<div class="sidebar-nav">
			<h2 class="h-extrasmall"><?php echo JText::_('COM_CPANEL_HEADER_SUBMENU'); ?></h2>
			<ul class="nav nav-list">
				<li class="active"><a href="<?php echo $this->baseurl; ?>"><?php echo JText::_('COM_CPANEL_LINK_DASHBOARD'); ?></a></li>
			</ul>
			<h3 class="h-extrasmall"><?php echo JText::_('COM_CPANEL_HEADER_SYSTEM'); ?></h3>
			<ul class="nav nav-list">
			<?php if($user->authorise('core.admin')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_config"><?php echo JText::_('COM_CPANEL_LINK_GLOBAL_CONFIG'); ?></a></li>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_admin&view=sysinfo"><?php echo JText::_('COM_CPANEL_LINK_SYSINFO'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.manage', 'com_cache')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_cache"><?php echo JText::_('COM_CPANEL_LINK_CLEAR_CACHE'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.admin', 'com_checkin')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_checkin"><?php echo JText::_('COM_CPANEL_LINK_CHECKIN'); ?></a></li>
			<?php endif;?>
			<?php if($user->authorise('core.manage', 'com_installer')):?>
				<li><a href="<?php echo $this->baseurl; ?>/index.php?option=com_installer"><?php echo JText::_('COM_CPANEL_LINK_EXTENSIONS'); ?></a></li>
			<?php endif;?>
			</ul>
		</div>
	</div>
	<div class="span9">
	<?php
	foreach ($this->modules as $module)
	{
		$output = JModuleHelper::renderModule($module, array('style' => 'well'));
		$params = new JRegistry;
		$params->loadString($module->params);
		echo $output;
	}
	?>
	</div>
</div>
