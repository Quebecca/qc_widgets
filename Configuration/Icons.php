<?php

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

/**
 * This Icon list that will return
 */
$iconsItems = [];

$iconsList = [
    'extension', 
    'dashboard', 
    'extensions', 
    'members', 
    'createdPages', 
    'modifiedPages', 
    'workspace', 
    'recentlyModifiedContent', 
    'pagesWithoutModification'
];

/**
 * Implement IconIdentifier using SVG
 */
foreach ($iconsList as $icon) {
    $iconsItems['tx-qc_widgets-' . $icon . '-icon'] = [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:qc_widgets/Resources/Public/Icons/' . \ucfirst($icon) . '.svg',
    ];
}

return $iconsItems;