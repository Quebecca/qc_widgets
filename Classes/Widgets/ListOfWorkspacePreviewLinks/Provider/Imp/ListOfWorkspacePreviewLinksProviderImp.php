<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider\Imp;

use Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider\ListOfWorkspacePreviewLinksProvider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class ListOfWorkspacePreviewLinksProviderImp implements ListOfWorkspacePreviewLinksProvider
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * @var string
     */
    protected string $orderField = '';

    /**
     * @var string
     */
    protected string $limit = '';

    /**
     * @var string
     */
    protected string $order= '';


    public function __construct(
        string $table,
        string $orderField,
        string $limit,
        string $order
    )
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
        $this->order = $order;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getItems(): array
    {
        // get the allowed workspcaes Uid
        $wsService = new WorkspaceService();
        // Doc
        $workspaces = $wsService->getAvailableWorkspaces();
        // render the workspace name and  title
        /*
        [
            "WS Title" => "My WS",
            "preview" => [
                         "link" => "tstamp",
                         "endTime" => "21-12-2022",
                         "keyword  ?" => "lsdsdgjbkljgbekrjgberkj"
                ]
        ]
        */
        return $this->renderData($workspaces);

    }

    public function renderData(array $worskpaces) : array {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('sys_preview')->createQueryBuilder();
        $previewsData = [];
        foreach ($worskpaces as $keyData => $value){
            // return the array og the sys_preview record
            $result = $queryBuilder
                ->select('tstamp','endtime', 'keyword')
                ->from('sys_preview')
                ->where(
                    $queryBuilder->expr()->like('config', "'%$keyData}'")
                )
                ->orderBy('endtime', 'DESC')
                ->setMaxResults(5)
                ->execute()
                ->fetchAll();

            // formatting date for the sys_preview records
            foreach ($result as $item){
                $previewsData[] = [
                    //'tstamp' => date("Y-m-d H:i:s", $item['crdate']),
                    'wsTitle' => $value,
                    'tstamp' => $item['endtime'],
                    'endtime' => date("Y-m-d H:i:s", $item['endtime']),
                    'keyword' => $item['keyword']
                ];
            }
            /* $workspacePreviewLink [] = [
                 "wsTitle" => $value,
                 "preview" => $previewsData
             ];*/
        }
        return $previewsData;
    }

}