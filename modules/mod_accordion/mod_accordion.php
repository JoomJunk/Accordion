 <?php
/**
 * @package    ##PACKAGE##
 * @author     ##AUTHOR## <##AUTHOREMAIL##>
 * @copyright  ##COPYRIGHT##
 * @license    ##LICENSE##
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';

$access = new stdClass;
$access->canEdit	= 0;
$access->canEditOwn = 0;
$access->canPublish = 0;

$list = ModAccordionHelper::getList30($params);
JHtml::_('jquery.framework');

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
	$arrow = '.jjaccordion .accordion-toggle:after { '
		. 'background: url(' . JURI::root() . 'media/mod_accordion/arrow-right.png) no-repeat;'
		. 'display: block;'
		. 'height: 15px; '
		. 'width: 15px; '
		. 'float: left; '
		. 'padding-right: 10px;'
		. '}'
		. '.jjaccordion .accordion-toggle collapsed:after { '
		. 'background: url(' . JURI::root() . 'media/mod_accordion/arrow-down.png) no-repeat;'
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
	default:
		// nothing here
}

$acc_id = $module->id;

if ($params->get('open', '1'))
{
	$open = $acc_id.'0';
}
else {
	$open = false;
}

if ($params->get('multi', '1'))
{
	$multi = false;
}
else {
	$multi = $acc_id;
}

require JModuleHelper::getLayoutPath('mod_accordion', 'default');
?>
