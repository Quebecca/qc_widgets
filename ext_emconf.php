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
            'typo3' => '10.4.0-11.5.99',
            'fluid_styled_content' => '10.4.0-11.5.99',
            'rte_ckeditor' => '10.4.0-11.5.99',
            'workspaces' => '10.4.0-11.5.99'
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
    'clearCacheOnLoad' => true,
    'version' => '1.1.0',
];
