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
namespace Qc\QcWidgets\Widgets\LastCreatedPages\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\DataHandling\History\RecordHistoryStore;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LastCreatedPagesProviderImp extends ListOfPagesProvider
{
    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit, $orderType);
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['lastCreatedPages.']['limit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * This function returns the array of records after rendering results from the database
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getItems(): array
    {
        $membersUid = [];
        // get groups
        $groupsUid = GeneralUtility::trimExplode(',', $GLOBALS['BE_USER']->user['usergroup']);
        // get uid of members
        foreach ($groupsUid as $groupUid){
            // Returns an array with UID records of all user NOT DELETED sorted by their username
            $data =  BackendUtility::getUserNames('uid', "AND usergroup LIKE  '%$groupUid%'  AND disable = 0");
            foreach($data as $key => $val){
                // if a user is present in two groups, we will have duplicated user uids
                // prevent the duplicated users uid
                if(!in_array($key, $membersUid)){
                    $membersUid[] = $key;
                }
            }
        }

        $historyQueryBuilder = $this->generateQueryBuilder("sys_history");
        $historyConstraints = [
            $historyQueryBuilder->expr()->and(
                $historyQueryBuilder->expr()->eq('tablename', $historyQueryBuilder->createNamedParameter("pages")),
                $historyQueryBuilder->expr()->in('userid', $historyQueryBuilder->createNamedParameter($membersUid,  Connection::PARAM_INT_ARRAY)),
                $historyQueryBuilder->expr()->eq('actiontype', $historyQueryBuilder->createNamedParameter(RecordHistoryStore::ACTION_ADD, Connection::PARAM_INT))
            )
           ];
        $results = $this->getRecordHistoryByUser($historyQueryBuilder, $historyConstraints, $this->limit);

        $pagesUids = [];
        foreach ($results as $result){
            $pagesUids[] = $result['recuid'];
        }

        // return results
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $constraints = [];
        if(!empty($membersUid)){
            $constraints = [
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($pagesUids,  Connection::PARAM_INT_ARRAY)),
                    $queryBuilder->expr()->eq('t3ver_wsid', 0),
                    $queryBuilder->expr()->eq('deleted', 0)
                )
            ];
        }
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);

    }


}