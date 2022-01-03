<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class ListOfWorkspacePreviewLinksProviderImp extends Provider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/ListOfWorkspacePreviewLinks/locallang.xlf:';

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $tsConfigLimit = intval($this->userTS['ListOfWorkspaceProviderLinksLimit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        // get the allowed workspaces Uid
        $wsService = new WorkspaceService();
        // Doc
        $workspaces = $wsService->getAvailableWorkspaces();
        return $this->renderData($workspaces);
    }

    /**
     * @param array $workspaces
     * @return array
     */
    public function renderData(array $workspaces) : array {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_preview')->createQueryBuilder();
        $previewsData = [];
        foreach ($workspaces as $keyData => $value){
            // return the array og the sys_preview record
            $result = $queryBuilder
                ->select('tstamp','endtime', 'keyword')
                ->from('sys_preview')
                ->where(
                    $queryBuilder->expr()->like('config', "'%$keyData}'")
                )
                ->orderBy('endtime', 'DESC')
                ->setMaxResults($this->limit)
                ->execute()
                ->fetchAll();

            // formatting date for the sys_preview records
            foreach ($result as $item){
                $expired = $item['endtime'] !== ''? $item['endtime'] < time() ? 1 : 0 : 0;
                $previewsData[] = [
                    'tstamp' => date("Y-m-d H:i:s", $item['tstamp']),
                    'wsTitle' => $value,
                    //'tstamp' => $item['endtime'],
                    'endtime' => date("Y-m-d H:i:s", $item['endtime']),
                    'keyword' => $item['keyword'],
                    'expired' => $expired
                ];
            }
        }
        return $previewsData;
    }

    /**
     * This function return the widget title
     * @return string
     */
    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'listOfMyWorkspaceLinks');
    }
}