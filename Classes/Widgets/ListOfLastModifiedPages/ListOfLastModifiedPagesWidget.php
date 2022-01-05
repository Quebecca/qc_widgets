<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages;

use Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\ListOfLastModifiedPagesProviderImp;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastModifiedPagesWidget implements WidgetInterface, AdditionalCssInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var ListOfLastModifiedPagesProviderImp
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(

        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        ListOfLastModifiedPagesProviderImp $dataProvider
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