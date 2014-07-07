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
	$arrow = '.jjaccordion .jjaccordion-wrapper .jjaccordion-header .jjaccordion-arrow { '
		. 'background: url(' . JUri::root() . 'media/mod_accordion/arrow-right.png) no-repeat;'
		. 'display: block;'
		. 'height: 15px; '
		. 'width: 15px; '
		. 'float: left; '
		. 'padding-right: 10px;'
		. '}'
		. '.jjaccordion .jjaccordion-wrapper.jjopen .jjaccordion-header .jjaccordion-arrow { '
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
	$open = " var first = $('.jjaccordion').find('.jjaccordion-content').slice(0,1);				
			  first.parents('.jjaccordion-wrapper').addClass('jjopen');	
			  first.slideDown();				
			";
}
else 
{
	$open = "";
}

$document->addScriptDeclaration("

	jQuery(document).ready(function($){	
		
		" . $open . "		

		$('.jjaccordion-header').click(function() {
							
			var self = $(this);
			var accordion = self.parents('.jjaccordion');
			var wrapper = self.parents('.jjaccordion-wrapper');
			var content = wrapper.children('.jjaccordion-content');
			var allWrapper = accordion.children('.jjaccordion-wrapper');
			var allContent = allWrapper.find('.jjaccordion-content');
						
			allWrapper.removeClass('jjopen');
			allContent.slideUp();
			
			wrapper.addClass('jjopen');
			content.stop(true, false).slideDown();	
			
		});
			
	});
	
");

require JModuleHelper::getLayoutPath('mod_accordion', 'default');
?>
