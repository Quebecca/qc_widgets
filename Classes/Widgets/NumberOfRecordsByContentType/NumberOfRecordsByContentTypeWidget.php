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

namespace Qc\QcWidgets\Widgets\NumberOfRecordsByContentType;

use Qc\QcWidgets\Widgets\AdditionalCssImp;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class NumberOfRecordsByContentTypeWidget extends AdditionalCssImp implements WidgetInterface
{
    /**
     * @var Provider
     */
    protected $dataProvider;

    public function __construct(
        private readonly WidgetConfigurationInterface $configuration,
        private readonly StandaloneView $view,
        Provider $dataProvider
    )
    {
        $this->dataProvider = $dataProvider;
    }

    /**
     * Render widget view
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->setTemplate('Widget/NumberOfRecordsByContent');
        $this->view->assignMultiple([
            'widgetTitle' => $widgetTitle,
            'data' => $data,
            'totalRecordsByNumberOfDays' =>  $this->dataProvider->getTotalRecordsByNumberOfDays()
        ]);
        return $this->view->render();
    }
}