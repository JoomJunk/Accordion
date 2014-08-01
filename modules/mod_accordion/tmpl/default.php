 <?php
/**
 * @package    ##PACKAGE##
 * @author     ##AUTHOR## <##AUTHOREMAIL##>
 * @copyright  ##COPYRIGHT##
 * @license    ##LICENSE##
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="jjaccordion">
	<?php
	
	echo JHtml::_('bootstrap.startAccordion', $acc_id, array(
		'active' => $open
	));
	
	for ($i = 0; $i < $items; $i++)
	{
		$listitem = $list[$i];
		$item_class = "item" . ($i + 1);

		if ($i == 0)
		{
			$item_class .= " first";
		}

		if ($i == $items - 1)
		{
			$item_class .= " last";
		}
		
		echo JHtml::_('bootstrap.addSlide', $multi, $listitem->title, $acc_id.$i);

		
				if ($params->get('image') && !empty($listitem->images))
				{
					$images = json_decode($listitem->images);

					if (!empty($images->image_intro))
					{
						echo '<p><img src="' . $images->image_intro . '" alt="" /></p>';
					}
				}

				if ($params->get('readmore'))
				{
					$listitem->introtext = ModAccordionHelper::truncate($listitem->introtext, $params->get('textlimit', 25), $ending = '...', false, true, false);
				}

				echo '<div>' . $listitem->introtext . '</div>';

				if (isset($listitem->link) && $params->get('readmore'))
				{
					echo '<br /><a class="readmore btn btn-small btn-primary" href="' . $listitem->link . '">' . JText::_('MOD_ACCORDION_READ_MORE') . '</a>';
				}

				
				
		echo JHtml::_('bootstrap.endSlide');
	}
	echo JHtml::_('bootstrap.endAccordion');
	?>
</div>