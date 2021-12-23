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
        $members = [];
        foreach ($data as $item){
            $item['tstamp'] = date("Y-m-d H:i:s", $item['tstamp']);
            if($item['realName'] == ''){
                $item['realName'] = 'Not provided';
            }
            if($item['email'] == ''){
                $item['email'] = 'Not provided';
            }
            array_push($members, $item);
        }
        $this->view->setTemplate('Widget/ListOfMembersWidget');
        $this->view->assign('members', $members);
        return $this->view->render();
    }
}