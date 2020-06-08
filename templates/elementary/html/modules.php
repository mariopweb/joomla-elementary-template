<?php

/**
 * @version $Id: modules.php 5556 2006-10-23 19:56:02Z Jinx $
 * @package Joomla
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// No direct access
defined('_JEXEC') or die();
function modChrome_elementaryMainMenu($module, &$params, &$attribs)
{
    $moduleTag = $params->get('module_tag', 'div');
    $moduleContainerCustomClass = 'elementaryModuleConnainer';

    if ($module->content) {
        echo '<' . $moduleTag . ' class="card border-0 mb-3 ' . $moduleContainerCustomClass . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
        echo '<nav class="navbar navbar-expand-lg navbar-dark">';
        echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbarSupporterContent" aria-controls="mainNavbarSupporterContent" aria-expanded="false" aria-label="Main menu toggler button">';
        echo 'Menu';
        echo '</button>';
        echo '<div class="collapse navbar-collapse" id="mainNavbarSupporterContent">';
        echo $module->content;
        echo '</div>';
        echo '</nav>';
        echo '</' . $moduleTag . '>';
    }
}


function modChrome_elementaryMod($module, &$params, &$attribs)
{
    $moduleTag = $params->get('module_tag', 'div');
    $headerLevel = $params->get('header_tag', 'h3');
    $headerStyle = htmlspecialchars($params->get('header_class', ''));
    $moduleContainerCustomClass = 'elementaryModuleConnainer';
    $moduleHeaderCustomClass = 'elementaryModuleHeader';
    $moduleBodyThemeClass = '';
    $themeColors = ['red', 'green'];
    $matchModule = (str_replace($themeColors, '', $params->get('moduleclass_sfx')) != $params->get('moduleclass_sfx'));
    if ($matchModule) {

        preg_match('/red|green/', $params->get('moduleclass_sfx'), $matchesModule);
        $moduleBodyThemeClass = 'bg-' . $matchesModule[0];
        // var_dump($matchesModule[0]);

        # code...
    }

    if ($module->content) {
        # code...
        if (strpos($params->get('class_sfx'), 'nav-pills') !== false && ($module->position == 'left-menu' || $module->position == 'right-menu')) {
            echo '<' . $moduleTag . ' class="card border-0 mb-3 ' . $moduleContainerCustomClass . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
        } else {
            echo '<' . $moduleTag . ' class="card mb-3 ' . $moduleContainerCustomClass . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
        }

        // var_dump($module);

        if ($module->showtitle) {
            # code...
            echo '<' . $headerLevel . ' class="card-header ' . $moduleHeaderCustomClass . $headerStyle . '">' . $module->title . '</' . $headerLevel . '>';
        }

        if (strpos($params->get('class_sfx'), 'navbar-nav') !== false && ($module->position == 'left-menu' || $module->position == 'right-menu')) {
            # code...
            echo '<nav class="navbar navbar-expand-lg navbar-dark">';
            echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#leftNavbarSupporterContent" aria-controls="leftNavbarSupporterContent" aria-expanded="false" aria-label="Menu toggler button">';
            echo 'Menu';
            echo '</button>';
            echo '<div class="collapse navbar-collapse" id="leftNavbarSupporterContent">';
            echo $module->content;
            echo '</div>';
            echo '</nav>';
            echo '</' . $moduleTag . '>';
        } else {
            echo '<' . $moduleTag . ' class="card-body">' . $module->content . '</' . $moduleTag . '>';
            echo '</' . $moduleTag . '>';
        }
    }
    // var_dump($params);
}
