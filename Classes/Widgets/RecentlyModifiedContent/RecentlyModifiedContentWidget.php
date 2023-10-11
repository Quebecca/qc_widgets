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
namespace Qc\QcWidgets\Widgets\RecentlyModifiedContent;


use Qc\QcWidgets\Widgets\AdditionalCssImp;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class RecentlyModifiedContentWidget extends AdditionalCssImp implements WidgetInterface
{
    public function __construct(
        protected WidgetConfigurationInterface $configuration,
        protected StandaloneView $view,
        protected Provider $dataProvider
    ){}

    /**
     * Render widget view
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->assignMultiple([
            'widgetTitle' => $widgetTitle,
            'data' => $data
        ]);
        return $this->view->render("Widget/RecentlyModifiedContentWidget");
    }

    public function getOptions(): array
    {
        return [];
    }
}
