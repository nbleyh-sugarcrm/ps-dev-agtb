<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');
/*********************************************************************************
 *The contents of this file are subject to the SugarCRM Professional End User License Agreement
 *("License") which can be viewed at http://www.sugarcrm.com/EULA.
 *By installing or using this file, You have unconditionally agreed to the terms and conditions of the License, and You may
 *not use this file except in compliance with the License. Under the terms of the license, You
 *shall not, among other things: 1) sublicense, resell, rent, lease, redistribute, assign or
 *otherwise transfer Your rights to the Software, and 2) use the Software for timesharing or
 *service bureau purposes such as hosting the Software for commercial gain and/or for the benefit
 *of a third party.  Use of the Software may be subject to applicable fees and any use of the
 *Software without first paying applicable fees is strictly prohibited.  You do not have the
 *right to remove SugarCRM copyrights from the source code or user interface.
 * All copies of the Covered Code must include on each user interface screen:
 * (i) the "Powered by SugarCRM" logo and
 * (ii) the SugarCRM copyright notice
 * in the same form as they appear in the distribution.  See full license for requirements.
 *Your Warranty, Limitations of liability and Indemnity are expressly stated in the License.  Please refer
 *to the License for the specific language governing these rights and limitations under the License.
 *Portions created by SugarCRM are Copyright (C) 2004 SugarCRM, Inc.; All Rights Reserved.
 * $Id: additionalDetails.php 13782 2006-06-06 17:58:55Z majed $
 *********************************************************************************/
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty plugin:
 * This is a Smarty plugin to handle the creation of HTML List elements for Sugar Action Menus.
 * Based on the theme, the plugin generates a proper group of button lists.
 *
 * @param $params array - its structure is
 *     'buttons' => list of button htmls, such as ( html_element1, html_element2, ..., html_element_n),
 *     'id' => id property for ul element
 *     'class' => class property for ul element
 * 	   'flat' => controls the display of the menu as a dropdown or flat buttons (if the value is assigned, it will be not affected by enable_action_menu setting.)
 * @param $smarty
 *
 * @return string - compatible sugarActionMenu structure, such as
 * <ul>
 *     <li>html_element1
 *         <ul>
 *              <li>html_element2</li>
 *                  ...
 *              </li>html_element_n</li>
 *         </ul>
 *     </li>
 * </ul>
 * ,which is generated by @see function smarty_function_sugar_menu
 *
 * <pre>
 * 1. SugarButton on smarty
 *
 * add appendTo to generate button lists
 * {{sugar_button ... appendTo='buttons'}}
 *
 * ,and then create menu
 * {{sugar_action_menu ... buttons=$buttons ...}}
 *
 * 2. Code generate in PHP
 * <?php
 * ...
 *
 * $buttons = array(
 *      '<input ...',
 *      '<a href ...',
 *      ...
 * );
 * require_once('include/SugarSmarty/plugins/function.sugar_action_menu.php');
 * $action_button = smarty_function_sugar_action_menu(array(
 *     'id' => ...,
 *     'buttons' => $buttons,
 *     ...
 * ),$xtpl);
 * $template->assign("ACTION_BUTTON", $action_button);
 * ?>
 * 3. Passing array to smarty in PHP
 * $action_button = array(
 *      'id' => 'id',
 *      'buttons' => array(
 *          '<input ...',
 *          '<a href ...',
 *          ...
 *      ),
 *      ...
 * );
 * $tpl->assign('action_button', $action_button);
 * in the template file
 * {sugar_action_menu params=$action_button}
 *
 * 4. Append button element in the Smarty
 * {php}
 * $this->append('buttons', "<a ...");
 * $this->append('buttons', "<input ...");
 * {/php}
 * {{sugar_action_menu ... buttons=$buttons ...}}
 * </pre>
 *
 * @author Justin Park (jpark@sugarcrm.com)
 */
function smarty_function_sugar_action_menu($params, &$smarty)
{
    global $sugar_config;

    if( !empty($params['params']) ) {
        $addition_params = $params['params'];
        unset($params['params']);
        $params = array_merge_recursive($params, $addition_params);
    }
    $flat = isset($params['flat']) ? $params['flat'] : (isset($sugar_config['enable_action_menu']) ? !$sugar_config['enable_action_menu'] : false);
    //if buttons have not implemented, it returns empty string;
    if(empty($params['buttons']))
        return '';

    if(is_array($params['buttons']) && !$flat) {

        $menus = array(
            'html' => array_shift($params['buttons']),
            'items' => array()
        );

        foreach($params['buttons'] as $item) {
            if(is_array($item)) {
                $sub = array();
                $sub_first = array_shift($item);
                foreach($item as $subitem) {
                    $sub[] = array(
                        'html' => $subitem
                    );
                }
                array_push($menus['items'],array(
                    'html' => $sub_first,
                    'items' => $sub,
                    'submenuHtmlOptions' => array(
                        'class' => 'subnav-sub'
                    )
                ));
            } else if(strlen($item)) {
                array_push($menus['items'],array(
                   'html' => $item
                ));
            }
        }
        $action_menu = array(
            'id' => !empty($params['id']) ? (is_array($params['id']) ? $params['id'][0] : $params['id']) : '',
            'htmlOptions' => array(
                'class' => !empty($params['class']) && strpos($params['class'], 'clickMenu') !== false  ? $params['class'] : 'clickMenu '. (!empty($params['class']) ? $params['class'] : ''),
                'name' => !empty($params['name']) ? $params['name'] : '',
            ),
            'itemOptions' => array(
                'class' => (count($menus['items']) == 0) ? 'single' : 'sugar_action_button'
            ),
            'submenuHtmlOptions' => array(
                'class' => 'subnav'
            ),
            'items' => array(
                $menus
            )
        );
        require_once('function.sugar_menu.php');
        return smarty_function_sugar_menu($action_menu, $smarty);

    }

    if (is_array($params['buttons'])) {
        return '<div class="action_buttons">' . implode_r(' ', $params['buttons'], true).'<div class="clear"></div></div>';
    } else if(is_array($params)) {
        return '<div class="action_buttons">' . implode_r(' ', $params, true).'<div class="clear"></div></div>';
    }

    return $params['buttons'];
}

function implode_r($glue, $pieces, $extract_first_item = false) {
    $result = array_shift($pieces);
    if(is_array($result)) {
        $result = implode_r($glue, $result);
    }
    foreach($pieces as $item) {
        if(is_array($item)) {
            $result .= empty($extract_first_item) ? implode_r($glue, $item) : $glue.$item[0];
        } else {
            $result .= $glue.$item;
        }
    }
    return $result;
}
?>
