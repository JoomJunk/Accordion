<?php 
/**
* @package		JJ Accordion
* @author		JoomJunk
* @copyright	Copyright (C) 2011 - 2012 JoomJunk. All Rights Reserved
* @license		http://www.gnu.org/licenses/gpl-3.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); 

?>
<div class="accordion" id="accordion">
	<?php for ($i=0; $i < $items; $i++) : ?>
			<div>
				<?php
				$listitem = $list[$i];
				$item_class = "item" . ($i + 1);
				if ($i == 0) $item_class .= " first";
				if ($i == $items - 1) $item_class .= " last";
				?>
				
				<h3 class="jjheader">
					<?php echo $listitem->title ?>
				</h3>
				
				<div class="accordion-section">
					<div class="accordion-content">
						<?php 		
						if(version_compare(JVERSION,'3.0','ge')) {
								$item = modAccordionHelper::renderItem30($listitem, $params, $access);
							if ($params->get('readmore')) {
								$words=$params->get('textlimit', 25);
								$counter = count(explode(" ",$item->introtext));
								if($counter>$words) {
									$array=array();
									$array = explode(" ", $item->introtext);
									array_splice($array, $words);
									$item->introtext = implode(" ", $array)." ...<br />";
								}
							}						

							echo $item->introtext;
							
							if (isset($item->link) && $item->readmore != 0 && $params->get('readmore')) :
								echo '<a class="readmore btn btn-small btn-primary" href="'.$item->link.'">'.$item->linkText.'</a>';
							endif;
						} 
						else {
							if ($params->get('readmore')) {
								$words=$params->get('textlimit', 25);
								$counter = count(explode(" ",$listitem->introtext));
								if($counter>$words) {
									$array=array();
									$array = explode(" ", $listitem->introtext);
									array_splice($array, $words);
									$listitem->introtext = implode(" ", $array)." ...<br />";
								}
							}
						
							echo $listitem->introtext;
						
							if (isset($listitem->link) && $params->get('readmore')) {
								if ($params->get('jj_style') == 'bootstrap') {
									echo '<br /><a class="readmore btn btn-small btn-primary" href="'.$listitem->link.'">' . JText::_('MOD_ACCORDION_READ_MORE') . '</a>';
								}
							else {
								echo '<br /><a class="readmore" href="'.$listitem->link.'">' . JText::_('MOD_ACCORDION_READ_MORE') . '</a>';
							}
							}
						}
						?>
					</div>
				</div>
			</div>
	<?php endfor; ?>
</div>

<?php
if($params->get('open', '1')) { $open=0;} else {$open=1-2;}
if($params->get('multi', '1')) { $multi=0;} else {$multi=1;}
?>

<script type="text/javascript">
	var parentAccordion=new JJ.accordion.slider('parentAccordion'); 
	parentAccordion.init('accordion','h3',<?php echo $multi; ?>,<?php echo $open; ?>,'jjheader');
</script>