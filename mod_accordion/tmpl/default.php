<?php 
/**
 * @package    JJ_Accordion
 * @author     JoomJunk <admin@joomjunk.co.uk>
 * @copyright  Copyright (C) 2011 - 2014 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="jjaccordion" id="accordion<?php echo $id; ?>">
	<?php
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
		?>

		<h3 class="jjaccordion-header"><span class="jjaccordion-arrow"></span><?php echo $listitem->title; ?></h3>

		<div class="jjaccordion-content">
			<?php
				if ($params->get('image') && !empty($listitem->images))
				{
					$images = json_decode($listitem->images);

					if (!empty($images->image_intro) && !empty($images->image_intro_alt))
					{
						echo '<image src="' . $images->image_intro . '" alt="' . $images->image_intro_alt . '" height="20px" />';
					}
				}

				if ($params->get('readmore'))
				{
					$listitem->introtext = ModAccordionHelper::truncate($listitem->introtext, $params->get('textlimit', 25), $ending = '...', false, true, false);
				}

				echo $listitem->introtext;

				if (isset($listitem->link) && $params->get('readmore'))
				{
					echo '<br /><a class="readmore btn btn-small btn-primary" href="' . $listitem->link . '">' . JText::_('MOD_ACCORDION_READ_MORE') . '</a>';
				}
			?>
		</div>
	<?php
	}
	?>
</div>