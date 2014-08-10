<?php
/**
 * @package    JJ_Accordion
 * @author     JoomJunk <admin@joomjunk.co.uk>
 * @copyright  Copyright (C) 2011 - 2013 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

$id = $module->id;

$access = new stdClass;
$access->canEdit	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;

if (version_compare(JVERSION, '3.0', 'ge'))
{
	$list = ModAccordionHelper::getList30($params);
	JHtml::_('jquery.framework');
}
else
{
	$list = ModAccordionHelper::getList25($params);
	if (!JFactory::getApplication()->get('jquery'))
	{
		JFactory::getApplication()->set('jquery', true);
		JHtml::_('script', JUri::root() . 'media/mod_accordion/js/jquery.js');
	}
}

$items = count($list);

if (!$items)
{
	echo JText::_('MOD_ACCORDION_NO_ARTICLES');

	return;
}

$jj_style = $params->get('jj_style', 'light');

$document = JFactory::getDocument();

if ($params->get('arrow', '1'))
{
	$arrow = '.jjaccordion .jjaccordion-arrow { '
		. 'background: url(' . JUri::root() . 'media/mod_accordion/arrow-right.png) no-repeat;'
		. 'display: block;'
		. 'height: 15px; '
		. 'width: 15px; '
		. 'float: left; '
		. 'padding-right: 10px;'
		. '}'
		. '.jjaccordion .jjaccordion-header.active-header .jjaccordion-arrow { '
		. 'background: url(' . JUri::root() . 'media/mod_accordion/arrow-down.png) no-repeat;'
		. '}';
	$document->addStyleDeclaration($arrow);
}

switch ($jj_style)
{
	case "custom":
		$document->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion.css');
		$jj_style = '.jjaccordion .jjaccordion-header {'
		. 'background:#' . $params->get('headerbg') . ';'
		. 'border: 1px solid #' . $params->get('headerbordercolor') . ';'
		. 'color:#' . $params->get('headertextcolor') . ';'
		. '}';
		$document->addStyleDeclaration($jj_style);
		break;
	case "dark":
		$document->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-dark.css');
		break;
	case "bootstrap":
		$document->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-bootstrap.css');
		break;
	default:
		$document->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-light.css');
}

if ($params->get('open', '1'))
{
	$open = "$('#accordion" . $id . " .jjaccordion-header').first().toggleClass('active-header').toggleClass('inactive-header');
			 $('#accordion" . $id . " .jjaccordion-content').first().slideDown().toggleClass('open-content');";
}
else 
{
	$open = "";
}

$document->addScriptDeclaration("
	(function($){
		$(document).ready(function(){

			$('#accordion" . $id . " .jjaccordion-header').toggleClass('inactive-header');

			" . $open . "

			$('#accordion" . $id . " .jjaccordion-header').click(function () {
				if($(this).is('#accordion" . $id . " .inactive-header')) {
					$('#accordion" . $id . " .active-header').toggleClass('active-header').toggleClass('inactive-header').next().slideToggle('fast').toggleClass('open-content');
					$(this).toggleClass('active-header').toggleClass('inactive-header');
					$(this).next().slideToggle('fast').toggleClass('open-content');
				}

				else {
					$(this).toggleClass('active-header').toggleClass('inactive-header');
					$(this).next().slideToggle('fast').toggleClass('open-content');
				}
			});

			return false;
		});
	})(jQuery);
");

require JModuleHelper::getLayoutPath('mod_accordion', 'default');
