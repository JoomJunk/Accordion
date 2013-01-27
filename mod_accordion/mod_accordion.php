<?php
/**
* @package		JJ Accordion
* @author		JoomJunk
* @copyright	Copyright (C) 2011 - 2012 JoomJunk. All Rights Reserved
* @license		http://www.gnu.org/licenses/gpl-3.0.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

require_once (dirname(__FILE__).'/helper.php');

$access = new stdClass();
$access->canEdit	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;
 
if(version_compare(JVERSION,'3.0','ge')) {
	$list = modAccordionHelper::getList30($params);
}
else {
	$list = modAccordionHelper::getList25($params);
}

$items = count($list);
if (!$items) {
	echo JText::_('ACCORDION_NO_ARTICLES');
	return;
}

$module_base = JURI::root() . 'modules/mod_accordion/';

$jj_style = $params->get('jj_style', 'light');

require(JModuleHelper::getLayoutPath('mod_accordion', 'default'));

$document = JFactory::getDocument();

switch ($jj_style) {
	case "custom":
   		$document->addStyleSheet($module_base . 'assets/accordion.css');
		$jj_style ='#accordion h3 {'
		. 'background:#' . $params->get('headerbg') . ';'
		. 'border: 1px solid #' . $params->get('headerbordercolor') . ';'
		. 'color:#' . $params->get('headertextcolor') . ';'
		. '}';
		$document->addStyleDeclaration($jj_style);
   		break;
	case "dark":
   		$document->addStyleSheet($module_base . 'assets/accordion-dark.css');
   		break;
	case "bootstrap":
   		$document->addStyleSheet($module_base . 'assets/accordion-bootstrap.css');
   		break;
	default:
		$document->addStyleSheet($module_base . 'assets/accordion-light.css');	
}

$document->addScript($module_base . 'assets/accordion.js');
?>