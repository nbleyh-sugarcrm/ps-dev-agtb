<?php

$subpanel = MetaDataManager::getLayout("FilterPanelLayout");
$subpanel->push(array("layout" => "subpanel"));

$layout = MetaDataManager::getLayout('SideBarLayout');
$layout->push('main', array('view'=>'record', "primary" => true));
$layout->push('main', array('layout'=>'extra-info'));
$layout->push('main', array("layout" => $subpanel->getLayout(true)));
$layout->push('side', array('layout'=>'sidebar'));
$layout->push('dashboard', array('layout' => 'dashboard', 'context' => array(
    'forceNew' => true,
    'module' => 'Home',
)));
$layout->push('preview', array('layout' => 'preview'));
$viewdefs['base']['layout']['record'] = $layout->getLayout();
