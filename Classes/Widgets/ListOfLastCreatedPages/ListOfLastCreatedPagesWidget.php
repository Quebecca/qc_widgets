<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages;

use Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\ListOfLastCreatedPagesProvider;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfLastCreatedPagesWidget implements WidgetInterface, AdditionalCssInterface
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
        $widgetTitle = $this->dataProvider->getWidgetTitle();
        $this->view->setTemplate('Widget/TableOfPagesWidget');
        $this->view->assign('widgetTitle', $widgetTitle);

        if(!empty($data)){
            $this->view->assign('data', $data);
        }
        else {
            $this->view->assign('empty', true);
        }
        return $this->view->render();
    }

    public function getCssFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',
        ];
    }
}