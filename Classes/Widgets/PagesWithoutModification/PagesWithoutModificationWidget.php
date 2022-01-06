<?php

namespace Qc\QcWidgets\Widgets\PagesWithoutModification;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class PagesWithoutModificationWidget implements WidgetInterface, AdditionalCssInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var Provider
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(

        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        Provider $dataProvider
    )
    {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    /**
     * @return string
     */
    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();

        $this->view->setTemplate('Widget/TableOfPagesWidget');
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->assign('widgetTitle', $widgetTitle);
        $this->view->assign('data', $data);
        return $this->view->render();
    }


    public function getCssFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',
        ];
    }
}