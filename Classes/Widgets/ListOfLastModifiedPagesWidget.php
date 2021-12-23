<?php

namespace Qc\QcWidgets\Widgets;

use Qc\QcWidgets\Widgets\Provider\ListOfLastModifiedPagesProvider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastModifiedPagesWidget implements WidgetInterface
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
        $this->view->setTemplate('Widget/ListOfLastModifiedPagesWidget');
        return $this->view->render();
    }
}