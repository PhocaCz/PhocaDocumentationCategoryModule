<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
defined('_JEXEC') or die('Restricted access');// no direct access

$app		= JFactory::getApplication();
//$params 	= $app->getParams();
$user 		= JFactory::getUser();
$db			= JFactory::getDBO();
$userLevels	= implode (',', $user->getAuthorisedViewLevels());
$menu 		= $app->getMenu();
JHTML::stylesheet('media/com_phocadocumentation/css/phocadocumentation-transparent.css' );


$wheres		= array();


$display_categories 		= $params->get('display_categories', '');
$hide_categories 			= $params->get('hide_categories', '');
$display_empty_categories 	= $params->get('display_empty_categories', 0);

if ( $display_categories != '' ) {
	$category_ids_where = " AND s.id IN (".$display_categories.")";
} else {
	$category_ids_where = '';
}

if ( $hide_categories != '' ) {
	$category_ids_not_where = " AND s.id NOT IN (".$hide_categories.")";
} else {
	$category_ids_not_where = '';
}

$wheres[] = " cc.parent_id = 1";
$wheres[] = " cc.published = 1";
$wheres[] = " cc.extension = 'com_content'";

if ($display_empty_categories == 2) {
	$wheres[] = " cc.title <> 'Uncategorised'";
} else if ($display_empty_categories == 1) {
	
} else {
	//$wheres[] = " c.state = 1";
}
$wheres[] = " cc.access IN (".$userLevels.")";



$categoriesOrdering = 'lft';

$query =  " SELECT cc.id, cc.id AS id, cc.title, cc.description, cc.alias, cc.access"
		. " FROM #__categories AS cc"
		//. " LEFT JOIN #__content AS c ON c.catid = cc.id"
		. " WHERE " . implode( " AND ", $wheres )
		. " GROUP BY cc.id"
		. " ORDER BY cc.".$categoriesOrdering;
		
$db->setQuery( $query );
$categories = $db->loadObjectList();

// ITEMID
$itemCategory			= $menu->getItems('link', 'index.php?option=com_phocadocumentation&view=categories');
	
if(isset($itemCategory[0])) {
	$itemId = $itemCategory[0]->id;
} else {
	$itemId = JRequest::getVar('Itemid', 1, 'get', 'int');
}

$output = '<ul id="ph-pmod-box-module">';
if (!empty($categories)) {
	foreach ($categories as $value) {
		$output .= '<li class="ph-pdmod-categories">';
		$output .= '<a href="'. JRoute::_('index.php?option=com_phocadocumentation&view=category&id='.$value->id.':'.$value->alias.'&Itemid='.(int)$itemId ).'">'. $value->title.'</a></li>';
		//$output .= ' <small>('.$value->numcat.')</small></p>';
	}	
}
$output .= '</ul>';

require(JModuleHelper::getLayoutPath('mod_phocadocumentation_category'));
?>