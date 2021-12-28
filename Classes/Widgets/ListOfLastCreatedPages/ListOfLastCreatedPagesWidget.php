<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages;

use Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\ListOfLastCreatedPagesProvider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastCreatedPagesWidget implements WidgetInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var ListOfLastCreatedPagesProvider
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        ListOfLastCreatedPagesProvider $dataProvider
    )
    {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();
        $this->view->setTemplate('Widget/ListOfLastCreatedPagesWidget');
        $this->view->assign('data', $data);
        return $this->view->render();
    }
}