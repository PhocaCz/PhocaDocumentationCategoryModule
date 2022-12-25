<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');



echo '<div class="mod-phocadocumentation-category'.$moduleclass_sfx .'">';

if (!empty($articles)) {

    echo '<ul class="list-group list-group-flush pdoc-category-article-list">';
    foreach ($articles as $item) {

        echo '<li class="list-group-item">';
        echo '<svg class="pdoc-si pdoc-si-article"><use xlink:href="'.$pdoc->svg_path.'#pdoc-si-article"></use></svg>';

        $item->slug = $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
        $link = RouteHelper::getArticleRoute($item->slug, $item->categoryid, $item->language);
        echo '<a href="' . Route::_($link) . '">' . htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'). '</a>';
        //$output .= ' <small>('.$value->numcat.')</small></p>';

        echo '</li>';
    }
    echo '</ul>';
}


if (!empty($categories)) {
    echo '<ul class="list-group list-group-flush pdoc-category-category-list">';
    foreach ($categories as $item) {
        echo '<li class="list-group-item">';
        echo '<svg class="pdoc-si pdoc-si-category"><use xlink:href="'.$pdoc->svg_path.'#pdoc-si-category"></use></svg>';
        echo '<a href="' . Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)). '">' . htmlspecialchars($item->title, ENT_COMPAT, 'UTF-8'). '</a>';
        //$output .= ' <small>('.$value->numcat.')</small></p>';
        echo '</li>';
    }
    echo '</ul>';
}


echo'</div>';

?>


