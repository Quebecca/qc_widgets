<?php

/***
 *
 * This file is part of Qc Widgets project.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 <techno@quebec.ca>
 *
 ***/

namespace Qc\QcWidgets\Widgets\WorkspacePreviews\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class WorkspacePreviewProviderImp extends Provider
{
    /**
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
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE . 'listOfMyWorkspaceLinks'));
        $this->workspaceService = $workspaceService ?? GeneralUtility::makeInstance(WorkspaceService::class);
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['workspaceProviderLinks.']['limit']);
        // get the limit value from the tsconfig
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * This function returns the array of records after rendering results from the database
     * @return array
     * @throws Exception
     */
    public function getItems(): array
    {
        // get the allowed workspaces Uid
        // retrieves the available workspaces from the database and checks whether and they're available to the current BE user
        $workspaces = $this->workspaceService->getAvailableWorkspaces();
        return $this->renderData($workspaces);
    }

    /**
     * This function is used to return the data from the database
     * @param array $workspaces
     * @return array
     * @throws Exception
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
                ->fetchAllAssociative();
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

}