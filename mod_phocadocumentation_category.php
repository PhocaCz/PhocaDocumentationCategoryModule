<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */


defined('_JEXEC') or die('Restricted access');// no direct access

if (!JComponentHelper::isEnabled('com_phocadocumentation', true)) {
    echo '<div class="alert alert-danger">Phoca Documentation Error: Phoca Documentation component is not installed or not published on your system</div>';
    return;
}

if (!class_exists('PhocaDocumentationHelperRoute')) {
    require_once(JPATH_SITE . '/components/com_phocadocumentation/helpers/route.php');
}

$component 				= 'com_phocadocumentation';
$pC	 					= JComponentHelper::getParams($component);
$css					= $pC->get( 'theme', 'phocadocumentation-grey' );

$app = JFactory::getApplication();
//$params 	= $app->getParams();
$user = JFactory::getUser();
$db = JFactory::getDBO();
$userLevels = implode(',', $user->getAuthorisedViewLevels());
$menu = $app->getMenu();
JHTML::stylesheet('media/com_phocadocumentation/css/'.$css.'.css' );

$option = $app->input->get('option');
$view = $app->input->get('view');
$id = $app->input->get('id', 0);
$catid = $app->input->get('catid', 0);

$display_categories = $params->get('display_categories', '');
$hide_categories = $params->get('hide_categories', '');
$display_empty_categories = $params->get('display_empty_categories', 0);
$display_article_list = $params->get('display_article_list', 0);

$wheres = array();


if ($option == 'com_content' && $view == 'article' && $display_article_list == 1) {

    if ($catid > 0) {

        $wheres[] = " a.state = 1";
        $wheres[] = " c.published = 1";
        $wheres[] = " a.catid = " . (int)$catid;
        $query = " SELECT a.id, a.alias, a.title, c.id as categoryid, c.alias as categoryalias"
            . " FROM #__content AS a"
            . " LEFT JOIN #__categories AS c ON a.catid = c.id"
            . " WHERE " . implode(" AND ", $wheres)
            . " GROUP BY a.id"
            . " ORDER BY a.id";
        $db->setQuery($query);
        $articles = $db->loadObjectList();


        $output = '<div id="ph-pmod-box-module">';
        if (!empty($articles)) {
            foreach ($articles as $value) {
                $output .= '<div class="ph-document"><span class="glyphicon glyphicon-book"></span> ';
                $output .= '<a href="' . JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid, $value->categoryalias)) . '">' . $value->title . '</a></div>';
                //$output .= ' <small>('.$value->numcat.')</small></p>';
            }
        }
        $output .= '</div>';

    }

} else {

    if ($display_categories != '') {
        $wheres[] = "  cc.id IN (" . $display_categories . ")";
    }

    if ($hide_categories != '') {
        $wheres[] = " cc.id NOT IN (" . $hide_categories . ")";
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
    $wheres[] = " cc.access IN (" . $userLevels . ")";


    $categoriesOrdering = 'lft';

    $query = " SELECT cc.id, cc.id AS id, cc.title, cc.description, cc.alias, cc.access"
        . " FROM #__categories AS cc"
        //. " LEFT JOIN #__content AS c ON c.catid = cc.id"
        . " WHERE " . implode(" AND ", $wheres)
        . " GROUP BY cc.id"
        . " ORDER BY cc." . $categoriesOrdering;

    $db->setQuery($query);
    $categories = $db->loadObjectList();

    // ITEMID
    $itemCategory = $menu->getItems('link', 'index.php?option=com_phocadocumentation&view=categories');

    if (isset($itemCategory[0])) {
        $itemId = $itemCategory[0]->id;
    } else {
        $itemId = $app->input->get('Itemid', 1, 'int');
    }

    $output = '<div id="ph-pmod-box-module">';
    if (!empty($categories)) {
        foreach ($categories as $value) {
            $output .= '<div class="ph-category"><span class="glyphicon glyphicon-folder-close"></span> ';
            $output .= '<a href="' . JRoute::_('index.php?option=com_phocadocumentation&view=category&id=' . $value->id . ':' . $value->alias . '&Itemid=' . (int)$itemId) . '">' . $value->title . '</a></div>';
            //$output .= ' <small>('.$value->numcat.')</small></p>';
        }
    }
    $output .= '</div>';

}

require(JModuleHelper::getLayoutPath('mod_phocadocumentation_category'));
?>
