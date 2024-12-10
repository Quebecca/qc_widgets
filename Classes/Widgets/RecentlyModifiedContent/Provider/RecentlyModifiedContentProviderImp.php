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

namespace Qc\QcWidgets\Widgets\RecentlyModifiedContent\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\DataHandling\History\RecordHistoryStore;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class RecentlyModifiedContentProviderImp extends Provider
{
    /**
     * Overriding the LONG_FILE attribute
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/RecentlyModifiedContent/locallang.xlf:';


    /**
     * @var WorkspaceService
     */
    protected WorkspaceService $workspaceService;

    /**
     * @var PageRepository
     */
    protected $pagesRepository;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType,
        WorkspaceService $workspaceService = null
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $this->pagesRepository = $pagesRepository ?? GeneralUtility::makeInstance(PageRepository::class);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE . 'recentlyModifiedContent'));
        $this->workspaceService = $workspaceService ?? GeneralUtility::makeInstance(WorkspaceService::class);
        // get the limit value from the tsconfig
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['recentlyModifiedContent.']['limit']);
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
        $data =  $this->renderData();
        return $this->dataMap($data);
    }

    /**
     * This function is used to map the rendering data from the database, and add controls on these values
     * @param array $data
     * @return array
     */
    public function dataMap(array $data){
        $result = [];
        foreach ($data as $item){
            if($item['header'] === ''){
                $item['bodytext'] = trim($item['bodytext']);
                if(strlen($item['bodytext']) < 50 ){
                    $item['bodytext'] = substr($item['bodytext'],0, 50);
                }
                else{
                    $item['bodytext'] = substr($item['bodytext'],0, 50) . '...';
                }
            }
            $status = $this->getItemStatus($item['starttime'], $item['endtime'], $item['hidden']);
            $item['status'] = $status['status'];
            $item['statusMessage'] = $status['statusMessage'];
            $result [] = [
                'uid' => $item['uid'],
                'cType' => $item['cType'],
                'pid' => $item['pid'],
                'pageTitle' => $this->pagesRepository->getPage($item['pid'])['title'] ?? '',
                'header' => $item['header'],
                'bodytext' => strip_tags($item['bodytext']),
                'tstamp' =>  date("Y-m-d H:i:s", $item['tstamp']),
                'status' => $item['status'],
                'statusMessage' => $item['statusMessage']
            ];
        }
        return $result;
    }


    /**
     * This function returns the database records
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function renderData() : array {
        $historyQueryBuilder = $this->generateQueryBuilder("sys_history");
        $historyConstraints = [
            $historyQueryBuilder->expr()->and(
                $historyQueryBuilder->expr()->eq('userid', $historyQueryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'],\PDO::PARAM_INT)),
                $historyQueryBuilder->expr()->eq('tablename', $historyQueryBuilder->createNamedParameter("tt_content")),
                $historyQueryBuilder->expr()->neq('actiontype', $historyQueryBuilder->createNamedParameter(RecordHistoryStore::ACTION_DELETE, Connection::PARAM_INT)),
            )
        ];
        $tt_content_uids = $this->getRecordHistoryByUser($historyQueryBuilder, $historyConstraints, $this->limit);

        $elements = [];
        foreach ($tt_content_uids as $content_uid){
            $element = BackendUtility::getRecord(
                "tt_content", $content_uid['recuid'],
                'uid,cType,pid,starttime, endtime,header,bodytext,tstamp,hidden',
                "AND t3ver_wsid = 0"
            );
            if($element){
                $elements[] = $element;
            }
        }
        usort($elements, function ($element1, $element2){
            return $element1['tstamp'] < $element2['tstamp'];
        });
        return $elements;

    }

}
