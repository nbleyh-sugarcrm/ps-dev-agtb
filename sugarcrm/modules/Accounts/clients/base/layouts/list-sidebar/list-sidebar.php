<?php

$layout = MetaDataManager::getLayout('GenericLayout');
//$layout->push(array('view'=>'countrychart', 'context'=>array('source'=>'SalesByCountry')));
$viewdefs['Accounts']['base']['layout']['list-sidebar'] = $layout->getLayout();
