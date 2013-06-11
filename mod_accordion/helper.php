<?php
/**
* @package		JJ Accordion
* @author		JoomJunk
* @copyright	Copyright (C) 2011 - 2012 JoomJunk. All Rights Reserved
* @license		http://www.gnu.org/licenses/gpl-3.0.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once JPATH_SITE.'/components/com_content/router.php';
require_once JPATH_SITE.'/components/com_content/helpers/route.php';

if(version_compare(JVERSION,'3.0','ge')) {
	JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
}
else {
	JModel::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');
} 

class modAccordionHelper{

	//Joomla 2.5
	function renderItem25(&$item, &$params, &$access){

		$user 	=& JFactory::getUser();

		$item->text 	= $item->introtext;
		$item->groups 	= '';
		$item->readmore = (trim($item->fulltext) != '');
		$item->metadesc = '';
		$item->metakey 	= '';
		$item->access 	= '';
		$item->created 	= '';
		$item->modified = '';

		if ($params->get('readmore')){
				if ($item->access <= $user->get('aid', 0)) {
					$itemparams=new JParameter($item->attribs);
					$readmoretxt=$itemparams->get('readmore',JText::_('Read more text'));
					
					$item->linkOn = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug, $item->sectionid));
					$item->linkText = $readmoretxt;
				} else {
					$item->linkOn = JRoute::_('index.php?option=com_user&view=login');
					$item->linkText = JText::_('Login To Read More');
				}
		}
		
		if (!$params->get('image')) {
			$item->text = preg_replace( '/<img[^>]*>/', '', $item->text );
		}

		return $item;
	}
	public static function getList25(&$params){
		$articles = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);

		$articles->setState('list.start', 0);
		$articles->setState('list.limit', (int) $params->get('items', 0));
		$articles->setState('filter.published', 1);

		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$articles->setState('filter.access', $access);

		$catids = $params->get('catid');
		$articles->setState('filter.category_id.include', (bool) '1');
		
		if ($catids) {
			$articles->setState('filter.category_id', $catids);
		}

		$articles->setState('list.ordering', $params->get('show_order', 'a.ordering'));
		$articles->setState('list.direction', 'ASC');

		$articles->setState('filter.featured', 'show');
		$articles->setState('filter.author_id', 'created_by');
		$articles->setState('filter.author_id.include', '1');

		$articles->setState('filter.language', $app->getLanguageFilter());

		$items = $articles->getItems();

		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');

		if ($option === 'com_content' && $view === 'article') {
			$active_article_id = JRequest::getInt('id');
		}
		else {
			$active_article_id = 0;
		}

		foreach ($items as &$item){
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised)) {
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			 else {
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
			if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} elseif (JRequest::getInt('Itemid') > 0) { 
					$Itemid = JRequest::getInt('Itemid');
				}

				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
				}

			$item->active = $item->id == $active_article_id ? 'active' : '';

			if ($item->catid) {
				$item->displayCategoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
			}

			$item->displayReadmore = $item->alternative_readmore;
			if (!$params->get('image')) {
				$item->introtext = preg_replace( '/<img[^>]*>/', '', $item->introtext );
			}
		}

		return $items;
	} 
	
	//Joomla 3.x
	function renderItem30($item, $params, $access) {
		$user = JFactory::getUser();

		$item->text 	= $item->introtext;
		$item->groups 	= '';
		$item->readmore = (trim($item->introtext) != '');
		$item->metadesc = '';
		$item->metakey 	= '';
		$item->access 	= '';
		$item->created 	= '';
		$item->modified = '';

		if ($params->get('readmore')){
				if ($item->access <= $user->get('aid', 0)) {
					$item->readmore = strlen(trim($item->introtext));
					$item->slug = $item->id.':'.$item->alias;
					$item->catslug = $item->catid.':'.$item->category_alias;
					
					$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catid));
					$item->linkText = JText::_('Read More');
				} else {
					$item->linkOn = JRoute::_('index.php?option=com_user&view=login');
					$item->linkText = JText::_('Login To Read More');
				}
		} 
		
		if (!$params->get('image')) {
			$item->text = preg_replace( '/<img[^>]*>/', '', $item->text );
		}
		return $item;
	}

	public static function getList30($params){

		$articles = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$articles->setState('params', $appParams);

		$articles->setState('list.start', 0);
		$articles->setState('list.limit', (int) $params->get('items', 0));
		$articles->setState('filter.published', 1);

		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$articles->setState('filter.access', $access);

		$catids = $params->get('catid');
		$articles->setState('filter.category_id.include', (bool) '1');
		
		if ($catids) {
			$articles->setState('filter.category_id', $catids);
		}

		$articles->setState('list.ordering', $params->get('show_order', 'a.ordering'));
		$articles->setState('list.direction', 'ASC');

		$articles->setState('filter.featured', 'show');
		$articles->setState('filter.author_id', 'created_by');
		$articles->setState('filter.author_id.include', '1');

		$articles->setState('filter.language', $app->getLanguageFilter());

		$items = $articles->getItems();

		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');

		if ($option === 'com_content' && $view === 'article') {
			$active_article_id = JRequest::getInt('id');
		}
		else {
			$active_article_id = 0;
		}
		foreach ($items as &$item){
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised)) {
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			}
			 else {
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
			if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} elseif (JRequest::getInt('Itemid') > 0) { 
					$Itemid = JRequest::getInt('Itemid');
				}
				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
				}

			$item->active = $item->id == $active_article_id ? 'active' : '';

			if ($item->catid) {
				$item->displayCategoryLink = JRoute::_(ContentHelperRoute::getCategoryRoute($item->catid));
			}
			$item->displayReadmore = $item->alternative_readmore;
			if (!$params->get('image')) {
				$item->introtext = preg_replace( '/<img[^>]*>/', '', $item->introtext );
			}
		}
		return $items;
	}

	/**
	* Source: http://www.gsdesign.ro/blog/cut-html-string-without-breaking-the-tags/
	* Truncates text.
	*
	* Cuts a string to the length of $length and replaces the last characters
	* with the ending if the text is longer than length.
	*
	* @param string  $text String to truncate.
	* @param integer $length Length of returned string, including ellipsis.
	* @param string  $ending Ending to be appended to the trimmed string.
	* @param boolean $exact If false, $text will not be cut mid-word
	* @param boolean $considerHtml If true, HTML tags would be handled correctly
	* @return string Trimmed string.
	*/
	function truncate($text, $length = 25, $ending = '...', $exact = true, $considerHtml = true) {
		if ($considerHtml) {
			// if the plain text is shorter than the maximum length, return the whole text
			if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}

			// splits all html-tags to scanable lines
			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);

			$total_length = strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {
				// if there is any html-tag in this line, handle it and add it (uncounted) to the output
				if (!empty($line_matchings[1])) {
					// if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					// do nothing
					// if tag is a closing tag (f.e. </b>)
					} else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						// delete tag from $open_tags list
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					// if tag is an opening tag (f.e. <b>)
					} else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						// add tag to the beginning of $open_tags list
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					// add html-tag to $truncate'd text
					$truncate .= $line_matchings[1];
				}

				// calculate the length of the plain text part of the line; handle entities as one character
				$content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length> $length) {
					// the number of characters which are left
					$left = $length - $total_length;
					$entities_length = 0;
					// search for html entities
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						// calculate the real length of all entities in the legal range
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += strlen($entity[0]);
							} else {
								// no more characters left
								break;
							}
						}
					}
					$truncate .= substr($line_matchings[2], 0, $left+$entities_length);
					// maximum length is reached, so get off the loop
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}

				// if the maximum length is reached, get off the loop
				if($total_length>= $length) {
					break;
				}
			}
		} else {
			if (strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = substr($text, 0, $length - strlen($ending));
			}
		}

		// if the words shouldn't be cut in the middle...
		if (!$exact) {
			// ...search the last occurance of a space...
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				// ...and cut the text in this position
				$truncate = substr($truncate, 0, $spacepos);
			}
		}

		// add the defined ending to the text
		$truncate .= $ending;

		if($considerHtml) {
			// close all unclosed html-tags
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}
		return $truncate;
	}

}
