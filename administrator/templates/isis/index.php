<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.isis
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.0
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

$app   = JFactory::getApplication();
$doc   = JFactory::getDocument();
$lang  = JFactory::getLanguage();
$input = $app->input;
$user  = JFactory::getUser();

// Add Stylesheets
$doc->addStyleSheet('templates/' . $this->template . '/css/template.css');

// If Right-to-Left
if ($this->direction === 'rtl')
{
	$doc->addStyleSheet('../media/jui/css/bootstrap-rtl.css');
}

// Load specific language related CSS
$file = 'language/' . $lang->getTag() . '/' . $lang->getTag() . '.css';
if (JFile::exists($file))
{
	$doc->addStyleSheet($file);
}

$doc->addStyleSheet('../media/jui/css/chosen.css');

// Detecting Active Variables
$option   = $input->get('option', '');
$view     = $input->get('view', '');
$layout   = $input->get('layout', '');
$task     = $input->get('task', '');
$itemid   = $input->get('Itemid', '');
$sitename = $app->getCfg('sitename');

if ($task === "edit" || $layout === "form" )
{
	$fullWidth = 1;
}
else
{
	$fullWidth = 0;
}

$cpanel = $option === "com_cpanel";

// Adjusting content width
if ($cpanel)
{
	$span = "span8";
}
elseif ($this->countModules('left') && $this->countModules('right'))
{
	$span = "span6";
}
elseif ($this->countModules('left') && !$this->countModules('right'))
{
	$span = "span10";
}
elseif (!$this->countModules('left') && $this->countModules('right'))
{
	$span = "span8";
}
else
{
	$span = "span12";
}

// Logo file
if ($this->params->get('logoFile'))
{
	$logo = JURI::root() . $this->params->get('logoFile');
}
else
{
	$logo = $this->baseurl . "/templates/" . $this->template . "/images/logo.png";
}

$lang = JFactory::getLanguage();

// 1.5 or Core then 1.6 3PD
$lang->load('mod_status', JPATH_BASE, null, false, false) ||
$lang->load('mod_status', JPATH_BASE, $lang->getDefault(), false, false);

$db    = JFactory::getDbo();
$query = $db->getQuery(true);

// Get the number of frontend logged in users.
$query->clear();
$query->select('COUNT(session_id)');
$query->from('#__session');
$query->where('guest = 0 AND client_id = 0');

$db->setQuery($query);
$online_count = (int) $db->loadResult();
$online_num = '<span class="badge">' . $online_count . '</span>';

// Get the number of back-end logged in users.
$query->clear();
$query->select('COUNT(session_id)');
$query->from('#__session');
$query->where('guest = 0 AND client_id = 1');

$db->setQuery($query);
$admin_count = (int) $db->loadResult();
$count = '<span class="badge">' . $admin_count . '</span>';

$total_count = '<span class="badge">' . ($admin_count + $online_count) . '</span>';

$hideLinks = $input->getBool('hidemainmenu');

// Get the number of unread messages in your inbox.
$query	= $db->getQuery(true);
$query->select('COUNT(*)');
$query->from('#__messages');
$query->where('state = 0 AND user_id_to = '.(int) $user->get('id'));

$db->setQuery($query);
$unread = (int) $db->loadResult();

// Print the inbox message.
$messages = ($hideLinks ? '' : '<a href="'.JRoute::_('index.php?option=com_messages').'">').
	'<i class="icon-envelope"></i> '.
	JText::plural('MOD_STATUS_MESSAGES', $unread).
	($hideLinks ? '' : '</a>');

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="../media/jui/js/jquery.js"></script>
	<script src="../media/jui/js/bootstrap.js"></script>
	<script src="../media/jui/js/chosen.jquery.min.js"></script>
	<script src="../media/jui/js/jquery-ui.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
	</script>
	<jdoc:include type="head" />
	<?php
	// Template color
	if ($this->params->get('templateColor'))
	{
	?>
	<style type="text/css">
		.header, .navbar-inner, .nav-list > .active > a, .nav-list > .active > a:hover, .dropdown-menu li > a:hover, .dropdown-menu .active > a, .dropdown-menu .active > a:hover
		{
			background: <?php echo $this->params->get('templateColor');?>;
		}
		.navbar-inner{
			-moz-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			-webkit-box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
			box-shadow: 0 1px 3px rgba(0, 0, 0, .25), inset 0 -1px 0 rgba(0, 0, 0, .1), inset 0 30px 10px rgba(0, 0, 0, .2);
		}
	</style>
	<?php
	}
	?>
</head>

<body class="site <?php echo $option . " view-" . $view . " layout-" . $layout . " task-" . $task . " itemid-" . $itemid . " ";?>" data-spy="scroll" data-target=".subhead" data-offset="87">
	<!-- Top Navigation -->
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="<?php echo $this->baseurl; ?>"><img src="<?php echo $logo;?>" height="18" alt="<?php echo $sitename; ?>" /></a>
				<div class="nav-collapse">
					<jdoc:include type="modules" name="menu" style="none" />

					<ul class="<?php if ($this->direction == 'rtl') : ?>nav<?php else : ?>nav pull-right<?php endif; ?>">
						<li><a href=#" id="onlinecount"><?php echo $total_count; ?> Online</a></li>
						<li><a href="<?php echo JURI::root(); ?>" target="_blank"><i class="icon-share-alt"></i><?php echo JText::_('JGLOBAL_VIEW_SITE'); ?></a></li>
						<li><?php echo $messages; ?></li>
						<li class="dropdown"> <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php echo $user->username; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li class=""><a href="index.php?option=com_admin&task=profile.edit&id=<?php echo $user->id;?>"><?php echo JText::_('TPL_ISIS_EDIT_ACCOUNT');?></a></li>
								<li class="divider"></li>
								<li class=""><a href="<?php echo JRoute::_('index.php?option=com_login&task=logout&'. JSession::getFormToken() .'=1');?>"><?php echo JText::_('TPL_ISIS_LOGOUT');?></a></li>
							</ul>
						</li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
		</div>
	</div>
	<!-- Header -->
	<div class="header">
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span9">
					<h1 class="page-title"><a style="color: white;"><?php echo $sitename . '</a> - ' . JHtml::_('string.truncate', $app->JComponentTitle, 40, false, false);?></h1>
				</div>
				<div class="span3">
					<jdoc:include type="modules" name="searchload" style="none" />
				</div>
			</div>
		</div>
	</div>
	<?php
	if (!$cpanel)
	{
	?>
	<!-- Subheader -->
	<a class="btn btn-subhead" data-toggle="collapse" data-target=".subhead-collapse"><?php echo JText::_('TPL_ISIS_TOOLBAR');?> <i class="icon-wrench"></i></a>
	<div class="subhead-collapse">
		<div class="subhead">
			<div class="container-fluid">
				<div id="container-collapse" class="container-collapse"></div>
				<div class="row-fluid">
					<div class="span12">
						<jdoc:include type="modules" name="toolbar" style="no" />
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	}
	else
	{
	?>
	<div style="margin-bottom: 20px"></div>
	<?php
	}
	?>
	<!-- container-fluid -->
	<div class="container-fluid container-main">
		<div class="row-fluid">
			<?php if (($this->countModules('left')) && $cpanel): ?>
			<!-- Begin Sidebar -->
			<div id="sidebar" class="span2">
				<div class="sidebar-nav">
					<jdoc:include type="modules" name="left" style="no" />
				</div>
			</div>
			<!-- End Sidebar -->
			<?php endif; ?>
			<div id="content" class="<?php echo $span;?>">
				<!-- Begin Content -->
				<jdoc:include type="modules" name="top" style="xhtml" />
				<jdoc:include type="message" />
				<jdoc:include type="component" />
				<jdoc:include type="modules" name="bottom" style="xhtml" />
				<!-- End Content -->
			</div>
			<?php if (($this->countModules('right')) || $cpanel) : ?>
			<div id="aside" class="span4">
				<!-- Begin Right Sidebar -->
				<?php
				/* Load cpanel modules */
				if ($cpanel):?>
					<jdoc:include type="modules" name="icon" style="well" />
				<?php endif;?>
				<jdoc:include type="modules" name="right" style="xhtml" />
				<!-- End Right Sidebar -->
			</div>
			<?php endif; ?>
		</div>
		<hr />
		<?php if (!$this->countModules('status')): ?>
			<div class="footer">
				<p>&copy; <?php echo $sitename; ?> <?php echo date('Y');?></p>
			</div>
		<?php endif; ?>
	</div>
	<jdoc:include type="modules" name="debug" style="none" />
	<script>
		(function($){
			$('*[rel=tooltip]').tooltip()
			$('*[rel=popover]').popover()

			// fix sub nav on scroll
			var $win = $(window)
			  , $nav = $('.subhead')
			  , navTop = $('.subhead').length && $('.subhead').offset().top - 40
			  , isFixed = 0

			processScroll()

			// hack sad times - holdover until rewrite for 2.1
			$nav.on('click', function () {
				if (!isFixed) setTimeout(function () {  $win.scrollTop($win.scrollTop() - 47) }, 10)
			})

			$win.on('scroll', processScroll)

			function processScroll() {
				var i, scrollTop = $win.scrollTop()
				if (scrollTop >= navTop && !isFixed) {
					isFixed = 1
					$nav.addClass('subhead-fixed')
				} else if (scrollTop <= navTop && isFixed) {
					isFixed = 0
					$nav.removeClass('subhead-fixed')
				}
			}

			// Chosen select boxes
			$("select").chosen({
				disable_search_threshold : 10,
				allow_single_deselect : true
			});

			// Turn radios into btn-group
			$('.radio.btn-group label').addClass('btn')
			$(".btn-group label:not(.active)").click(function(){
				var label = $(this);
				var input = $('#' + label.attr('for'));

				if (!input.prop('checked')){
					label.closest('.btn-group').find("label").removeClass('active btn-primary');
					label.addClass('active btn-primary');
					input.prop('checked', true);
				}
			});
			$(".btn-group input[checked=checked]").each(function(){
				$("label[for=" + $(this).attr('id') + "]").addClass('active btn-primary');
			});

			var a = $("#onlinecount");
			a.popover({
				placement: 'bottom',
				trigger: 'manual',
				content: '<?php echo JText::plural('MOD_STATUS_USERS', $online_num) . '<br />' . JText::plural('MOD_STATUS_BACKEND_USERS', $count) ; ?>',
				title: 'Users online'
			});
			a.click(function(){
				$('#onlinecount').popover('toggle');
			});

		})(jQuery);
	</script>
</body>
</html>
