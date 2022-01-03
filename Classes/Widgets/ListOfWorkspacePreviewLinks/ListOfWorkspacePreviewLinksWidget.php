<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks;


use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfWorkspacePreviewLinksWidget implements WidgetInterface
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
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->setTemplate('Widget/ListOfWorkspacePreviewLinks');
        $this->view->assign('widgetTitle', $widgetTitle);
        if(!empty($data)){
            $this->view->assign('data', $data);
        }
        else {
            $this->view->assign('empty', true);
        }
        //$this->view->assign('data',$data);
        return $this->view->render();
    }

}