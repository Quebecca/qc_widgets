<?php

/**
 * Extension Manager/Repository config file for ext "qc_widgets".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Qc Widgets',
    'description' => 'Many widgets with info about pages and tt_content modifications, related to groups or current user and also a list of Workspace preview links.',
    'author' => 'Quebec.ca',
    'category' => 'module',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
            'fluid_styled_content' => '12.4.0-12.9.99',
            'rte_ckeditor' => '12.4.0-12.9.99',
            'workspaces' => '12.4.0-12.9.99'
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Qc\\QcWidgets\\' => 'Classes/'
        ],
    ],
    'state' => 'stable',
    'version' => '2.0.1',
];
