<?php

/**
 * Extension Manager/Repository config file for ext "qc_widgets".
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Qc widgets',
    'description' => 'Qc Widgets',
    'category' => 'templates',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
            'fluid_styled_content' => '10.4.0-10.4.99',
            'rte_ckeditor' => '10.4.0-10.4.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'Quebecca\\QcWidgets\\' => 'Classes',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 1,
    'author' => 'quebec.ca',
    'author_email' => 'quebec@mce.ca',
    'author_company' => 'Quebec.ca',
    'version' => '1.0.0',
];
