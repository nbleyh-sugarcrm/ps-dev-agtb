<?php

$viewdefs['Documents']['base']['filter']['default'] = array(
    'default_filter' => 'my_documents',
    'filters' => array(
        array(
            'id' => 'my_documents',
            'name' => translate('LBL_HOMEPAGE_TITLE', 'Documents'),
            'filter_definition' => array(
                '$creator' => '',
            ),
            'editable' => false
        ),
    )
);
