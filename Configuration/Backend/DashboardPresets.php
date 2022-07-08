<?php
return [
    'qcWidgets' => [
        'title' => 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:qcWidgets',
        'description' => 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:qcWidgetsDescription',
        'iconIdentifier' => 'tx-qc_widgets-dashboard-icon',
        'showInWizard' => true,
        'defaultWidgets' => ['listOfMembers', 'lastModifiedPages', 'lastCreatedPages', 'workspacePreviews', 'pagesWithoutModification','numberOfRecordsByContentType', 'recentlyModifiedContent']
    ]
];