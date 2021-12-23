<?php
namespace Qc\QcWidgets\Widgets;

use Qc\QcWidgets\Widgets\Provider\ListOfLastCreatedPagesProvider;
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
        $this->view->setTemplate('Widget/ListOfLastCreatedPagesWidget');
        return $this->view->render();
    }
}