<?php 
/**
 * @package    JJ_Accordion
 * @author     JoomJunk <admin@joomjunk.co.uk>
 * @copyright  Copyright (C) 2011 - 2012 JoomJunk. All Rights Reserved
 * @license    GNU General Public License version 3; http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

?>
<div class="accordion" id="accordion">
	<?php
	for ($i = 0; $i < $items; $i++)
	{
	?>
			<div>
				<?php
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

				<h3 class="jjheader">
					<?php echo $listitem->title ?>
				</h3>

				<div class="accordion-section">
					<div class="accordion-content">
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
				</div>
			</div>
	<?php
	}
	?>
</div>

<?php
if ($params->get('open', '1'))
{
	$open = 0;
}
else
{
	$open = 1 - 2;
}

if ($params->get('multi', '1'))
{
	$multi = 0;
}
else
{
	$multi = 1;
}
?>

<script type="text/javascript">
	var parentAccordion=new JJ.accordion.slider('parentAccordion');
	parentAccordion.init('accordion','h3',<?php echo $multi; ?>,<?php echo $open; ?>,'jjheader');
</script>