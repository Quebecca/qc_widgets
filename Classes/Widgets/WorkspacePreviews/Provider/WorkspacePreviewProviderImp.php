<?php
namespace Qc\QcWidgets\Widgets\WorkspacePreviews\Provider;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class WorkspacePreviewProviderImp extends Provider
{
    /**
     * Overriding the LONG_FILE attribute
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/WorkspacePreviews/locallang.xlf:';

    /**
     * @var WorkspaceService
     */
    protected WorkspaceService $workspaceService;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType,
        WorkspaceService $workspaceService = null
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $this->workspaceService = $workspaceService ?? GeneralUtility::makeInstance(WorkspaceService::class);
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
        // retrieves the available workspaces from the database and checks whether and they're available to the current BE user
        $workspaces = $this->workspaceService->getAvailableWorkspaces();
        return $this->renderData($workspaces);
    }

    /**
     * @param array $workspaces
     * @return array
     */
    public function renderData(array $workspaces) : array {
        $previewsData = [];
        $queryBuilder = $this->generateQueryBuilder($this->table);
        foreach ($workspaces as $keyData => $value){
            // return the array og the sys_preview records
            $result = $queryBuilder
                ->select('tstamp','endtime', 'keyword')
                ->from($this->table)
                ->where(
                    $queryBuilder->expr()->like('config', "'%$keyData}'")
                )
                ->orderBy($this->orderField, $this->orderType)
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
     * This function returns the widget title
     * @return string
     */
    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'listOfMyWorkspaceLinks');
    }
}