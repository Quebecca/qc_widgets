<?php

/***
 *
 * This file is part of Qc Widgets project.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 <techno@quebec.ca>
 *
 ***/
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