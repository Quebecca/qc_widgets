<?php

namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;

class AdditionalCssImp implements AdditionalCssInterface
{
    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',
        ];
    }
}