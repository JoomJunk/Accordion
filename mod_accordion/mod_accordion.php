<?php
/**
 * @package    JJ_Accordion
 * @author     JoomJunk <admin@joomjunk.co.uk>
 * @copyright  Copyright (C) 2011 - 2014 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
*/

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

$id  = $module->id;
$doc = JFactory::getDocument();
$app = JFactory::getApplication();

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
	if (!$app->get('jquery'))
	{
		$app->set('jquery', true);
		JHtml::_('script', 'mod_accordion/js/jquery.js', false, true);
	}
}

$items = count($list);

if (!$items)
{
	echo JText::_('MOD_ACCORDION_NO_ARTICLES');

	return;
}

// Params
$jj_style 	= $params->get('jj_style', 'light');
$open 		= $params->get('open', '1') == 1;
$headerbg	= $params->get('headerbg');
$headerbc	= $params->get('headerbordercolor');
$headertc	= $params->get('headertextcolor');
$arrow		= $params->get('arrow', '1');


if ($arrow)
{
	$arrow = '.jjaccordion .jjaccordion-arrow { '
		. 'background: url(' . JUri::root() . 'media/mod_accordion/arrow-right.png) no-repeat;'
		. 'display: inline-block;'
		. 'height: 15px; '
		. 'width: 15px; '
		. 'padding-right: 10px;'
		. 'vertical-align: middle;'
		. '}'
		. '.jjaccordion .jjaccordion-header.active-header .jjaccordion-arrow { '
		. 'background: url(' . JUri::root() . 'media/mod_accordion/arrow-down.png) no-repeat;'
		. '}';
	$doc->addStyleDeclaration($arrow);
}

switch ($jj_style)
{
	case "custom":
		$doc->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion.css');
		$jj_style = '.jjaccordion .jjaccordion-header {'
		. 'background:#' . $headerbg . ';'
		. 'border: 1px solid #' . $headerbc . ';'
		. 'color:#' . $headertc . ';'
		. '}';
		$doc->addStyleDeclaration($jj_style);
		break;
	case "dark":
		$doc->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-dark.css');
		break;
	case "bootstrap":
		$doc->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-bootstrap.css');
		break;
	default:
		$doc->addStyleSheet(JUri::root() . 'media/mod_accordion/css/accordion-light.css');
}


JHtml::_('script', 'mod_accordion/jjaccordion.js', false, true);

$doc->addScriptDeclaration("
	jQuery(document).ready(function($){
		$('#accordion" . $id . "').jjaccordion({
			'open' : " . $open . ", 
			'id': '" . $id . "'
		});
	});
");

require JModuleHelper::getLayoutPath('mod_accordion', 'default');
