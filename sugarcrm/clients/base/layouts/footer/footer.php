<?php

$viewdefs['base']['layout']['footer'] = array(
    'components' => array(
        'type' => 'simple',
        array(
            'view' => 'instance-picker'
        ),
        array(
            'view' => 'tour-action'
        ),
        array(
            'view' => 'footer-actions'
        )
    ),

);