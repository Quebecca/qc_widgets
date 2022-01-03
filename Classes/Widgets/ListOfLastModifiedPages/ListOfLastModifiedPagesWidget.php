<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages;

use Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\ListOfLastModifiedPagesProvider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalJavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastModifiedPagesWidget implements WidgetInterface, AdditionalCssInterface, AdditionalJavaScriptInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var ListOfLastModifiedPagesProvider
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(

        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        ListOfLastModifiedPagesProvider $dataProvider
    )
    {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();

        $this->view->setTemplate('Widget/TableOfPagesWidget');
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->assign('widgetTitle', $widgetTitle);

        if(!empty($data)){
            $this->view->assign('data', $data);
        }
        else {
            $this->view->assign('empty', true);
        }
        return $this->view->render();
    }

    public function getJsFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/JavaScript/listOfLastModifiedPages.js',
        ];
    }

    public function getCssFiles(): array
    {
        return [

        ];
    }
}