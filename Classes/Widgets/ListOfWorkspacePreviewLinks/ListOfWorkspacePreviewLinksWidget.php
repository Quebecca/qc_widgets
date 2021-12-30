<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks;


use Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider\ListOfWorkspacePreviewLinksProvider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfWorkspacePreviewLinksWidget implements WidgetInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var ListOfWorkspacePreviewLinksProvider
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        ListOfWorkspacePreviewLinksProvider $dataProvider
    )
    {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();
        $this->view->setTemplate('Widget/ListOfWorkspacePreviewLinks');
        $this->view->assign('data',$data);
        return $this->view->render();
    }

}