<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */


use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');// no direct access

$app        = Factory::getApplication();
$document   = $app->getDocument();
$user       = Factory::getUser();
$db         = Factory::getDBO();
$userLevels = implode(',', $user->getAuthorisedViewLevels());
$menu       = $app->getMenu();

$pdoc       = new stdClass();
$pdoc->svg_path = HTMLHelper::image('com_phocadocumentation/svg-definitions.svg', '', [], true, 1);
$wa         = $document->getWebAssetManager();
$wa->registerAndUseStyle('com_phocadocumentation.main', 'media/com_phocadocumentation/css/main.css', array('version' => 'auto'));

$option     = $app->input->get('option');
$view       = $app->input->get('view');
$id         = $app->input->get('id', 0);
$catid      = $app->input->get('catid', 0);

$display_categories         = $params->get('display_categories', '');
$hide_categories            = $params->get('hide_categories', '');
$display_empty_categories   = $params->get('display_empty_categories', 0);
$display_article_list       = $params->get('display_article_list', 0);
$moduleclass_sfx 			= htmlspecialchars((string)$params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');

$wheres     = [];
$articles   = [];
$categories = [];

if ($option == 'com_content' && $view == 'article' && $display_article_list == 1) {

    if ($catid > 0) {

        $wheres[] = " a.state = 1";
        $wheres[] = " c.published = 1";
        $wheres[] = " a.catid = " . (int)$catid;
        $query = " SELECT a.id, a.alias, a.title, c.id as categoryid, c.alias as categoryalias, a.language as language"
            . " FROM #__content AS a"
            . " LEFT JOIN #__categories AS c ON a.catid = c.id"
            . " WHERE " . implode(" AND ", $wheres)
            . " GROUP BY a.id, a.alias, a.title, c.id, c.alias"
            . " ORDER BY a.ordering ASC";

        $db->setQuery($query);
        $articles = $db->loadObjectList();

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

    $query = " SELECT cc.id, cc.id AS id, cc.title, cc.description, cc.alias, cc.access, cc.language"
        . " FROM #__categories AS cc"
        //. " LEFT JOIN #__content AS c ON c.catid = cc.id"
        . " WHERE " . implode(" AND ", $wheres)
        . " GROUP BY cc.id"
        . " ORDER BY cc." . $categoriesOrdering;

    $db->setQuery($query);
    $categories = $db->loadObjectList();
}

require(JModuleHelper::getLayoutPath('mod_phocadocumentation_category'));

?>
