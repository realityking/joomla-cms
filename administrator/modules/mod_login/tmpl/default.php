<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
$document = JFactory::getDocument();
?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login" class="form-inline">
	<fieldset class="loginform">
		<div class="control-group">
			<div class="controls">
			  <div class="input-prepend">
			    <span class="add-on"><i class="icon-user" rel="tooltip" data-placement="<?php echo $document->direction == 'rtl' ? 'right' : 'left'; ?>" title="<?php echo JText::_('JGLOBAL_USERNAME'); ?>"></i> <label for="mod-login-username" class="element-invisible"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label></span><input name="username" tabindex="1" id="mod-login-username" type="text" class="input-big" placeholder="<?php echo JText::_('JGLOBAL_USERNAME'); ?>" size="15" autofocus="autofocus" />
			  </div>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
			  <div class="input-prepend">
			    <span class="add-on"><i class="icon-lock" rel="tooltip" data-placement="<?php echo $document->direction == 'rtl' ? 'right' : 'left'; ?>" title="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" ></i> <label for="mod-login-password" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label></span><input name="passwd" tabindex="2" id="mod-login-password" type="password" class="input-big"  placeholder="<?php echo JText::_('JGLOBAL_PASSWORD'); ?>" size="15" />
			  </div>
			 </div>
		</div>
		<div class="control-group">
			<div class="controls">
				<div class="input-prepend">
					<span class="add-on"><i class="icon-comment" data-placement="<?php echo $document->direction == 'rtl' ? 'right' : 'left'; ?>"  rel="tooltip" title="<?php echo JText::_('MOD_LOGIN_LANGUAGE'); ?>"></i> <label for="lang" class="element-invisible"><?php echo JText::_('MOD_LOGIN_LANGUAGE'); ?></label></span><?php echo $langs; ?>
				</div>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<div class="btn-group pull-left">
					<button tabindex="3" class="btn btn-primary btn-large"><i class="icon-lock icon-white"></i> <?php echo JText::_('MOD_LOGIN_LOGIN'); ?></button>
				</div>
			</div>
		</div>
		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
