<?php
namespace Qc\QcWidgets\Widgets\ListOfMembers;

use Qc\QcWidgets\Widgets\ListOfMembers\Provider\ListOfMembersProvider;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Pagination\PaginationInterface;
use TYPO3\CMS\Core\Pagination\SimplePagination;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\Widgets\AdditionalCssInterface;
use TYPO3\CMS\Dashboard\Widgets\AdditionalJavaScriptInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetConfigurationInterface;
use TYPO3\CMS\Dashboard\Widgets\WidgetInterface;
use TYPO3\CMS\Fluid\View\StandaloneView;

class ListOfMembersWidget implements WidgetInterface, AdditionalCssInterface
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
        $this->view->assign('data',$data);
        return $this->view->render();
    }

    public function getCssFiles(): array
    {
        return [
            'EXT:qc_widgets/Resources/Public/Css/widgetstyle.css',
        ];
    }
}