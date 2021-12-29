<?php

declare(strict_types=1);

/**
 * Definitions for routes provided by EXT:backend
 */
return [
    // Delete Exclude Link
    'set_number_of_items' => [
        'path' => '/set_number_of_items',
        'target' =>  Qc\QcWidgets\Widgets\ListOfLastModifiedPages\ListOfLastModifiedPagesWidget::class . '::setNumberOfItems'
    ],
];
