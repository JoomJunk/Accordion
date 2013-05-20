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
}
