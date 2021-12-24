<?php
namespace Qc\QcWidgets\Widgets;

use Qc\QcWidgets\Widgets\Provider\ListOfMembersProvider;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfMembersWidget implements WidgetInterface
{
    /** @var WidgetConfigurationInterface */
    private $configuration;
    /**
     * @var ListOfMembersProvider
     */
    protected $dataProvider;

    /** @var StandaloneView */
    private $view;

    public function __construct(
        WidgetConfigurationInterface $configuration,
        StandaloneView $view,
        ListOfMembersProvider $dataProvider
    )
    {
        $this->configuration = $configuration;
        $this->view = $view;
        $this->dataProvider = $dataProvider;
    }

    public function renderWidgetContent(): string
    {
        $data = $this->dataProvider->getItems();

        $this->view->setTemplate('Widget/ListOfMembersWidget');
        $this->view->assign('data', $data);
        return $this->view->render();
    }
}