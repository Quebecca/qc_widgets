<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\ListOfLastModifiedPagesProvider;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\Response;
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

    /** @var int */
    private $numberOfItems;


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

    /**
     * This Function is delete the selected excluded link
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function setNumberOfItems(ServerRequestInterface $request): ResponseInterface{
        $urlParam = $request->getQueryParams();
        $this->numberOfItems = $urlParam['numberOfItems'];
        return new Response();
    }
    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();
        $this->view->setTemplate('Widget/ListOfLastModifiedPagesWidget');
        $this->view->assign('data', $data);
        $this->view->assign('numberOfItems', $this->numberOfItems);
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
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',

        ];
    }
}