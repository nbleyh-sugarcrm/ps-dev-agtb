<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/Resources/Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */




$GLOBALS['studioDefs']['Documents'] = [
    'LBL_DETAILVIEW'=>[
                'template'=>'xtpl',
                'template_file'=>'modules/Documents/DetailView.html',
                'php_file'=>'modules/Documents/DetailView.php',
                'type'=>'DetailView',
                ],
    'LBL_EDITVIEW'=>[
                'template'=>'xtpl',
                'template_file'=>'modules/Documents/EditView.html',
                'php_file'=>'modules/Documents/EditView.php',
                'type'=>'EditView',
                ],
    'LBL_LISTVIEW'=>[
                'template'=>'listview',
                'meta_file'=>'modules/Documents/listviewdefs.php',
                'type'=>'ListView',
                ],
    'LBL_SEARCHFORM'=>[
                'template'=>'xtpl',
                'template_file'=>'modules/Documents/SearchForm.html',
                'php_file'=>'modules/Documents/ListView.php',
                'type'=>'SearchForm',
                ],

];
