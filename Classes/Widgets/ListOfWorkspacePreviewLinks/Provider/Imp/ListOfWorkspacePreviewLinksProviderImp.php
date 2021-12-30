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

        $result = [];
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table)->createQueryBuilder();
        foreach ($worskpaces as $keyData => $value){
            // return the array og the sys_preview record
            $previewsResult = $queryBuilder
                ->select('tstamp','endtime', 'keyword')
                ->from($this->table)
                ->where(
                    $queryBuilder->expr()->like('config', "'%$keyData}'")
                )
                ->orderBy($this->orderField, 'DESC')
                ->setMaxResults($this->limit)
                ->execute()
                ->fetchAll();

            // formatting date for the sys_preview records
            // streaming ??
            foreach ($previewsResult as $item){
                $previewsResult['tstamp'] = $item['crdate'] = date("Y-m-d H:i:s", $item['crdate']);
            }

            $workspacePreviewLink [] = [
                "wsTitle" => $value,
                "preview" => $previewsResult
            ];
            $result[] = $workspacePreviewLink;
        }
        return $result;
    }

}