<?php
$viewdefs['KBDocuments']['portal']['layout']['records'] = array (
  'components' => array (
    array (
      'layout' => array (
        'components' => array (
          array (
            'layout' => array (
              'components' => array (
                array (
                  'layout' => 'list',
                ),
              ),
              'type' => 'simple',
              'name' => 'main-pane',
              'span' => 8,
            ),
          ),
          array (
            'layout' => array (
              'components' => array (
                array (
                  'layout' => 'list-sidebar',
                ),
              ),
              'type' => 'simple',
              'name' => 'side-pane',
              'span' => 4,
            ),
          ),
          array (
            'layout' => array (
              'components' => array (
              ),
              'type' => 'simple',
              'name' => 'dashboard-pane',
              'span' => 4,
            ),
          ),
          array (
            'layout' => array (
              'components' => array (
              ),
              'type' => 'simple',
              'name' => 'preview-pane',
              'span' => 8,
            ),
          ),
        ),
        'type' => 'default',
        'name' => 'sidebar',
        'span' => 12,
      ),
    ),
  ),
  'type' => 'simple',
  'name' => 'base',
  'span' => 12,
);
