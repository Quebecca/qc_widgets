<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastCreatedPagesWidget implements WidgetInterface, AdditionalCssInterface
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
        $this->view->setTemplate('Widget/TableOfPagesWidget');
        $this->view->assign('widgetTitle', $widgetTitle);
        $this->view->assign('data', $data);
        return $this->view->render();
    }

    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',
        ];
    }
}