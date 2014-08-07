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

$list = ModAccordionHelper::getList($params);
JHtml::_('jquery.framework');
JHtml::_('stylesheet', 'mod_accordion/css/accordion.css', array(), true);

$items = count($list);

if (!$items)
{
	echo JText::_('MOD_ACCORDION_NO_ARTICLES');

	return;
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
