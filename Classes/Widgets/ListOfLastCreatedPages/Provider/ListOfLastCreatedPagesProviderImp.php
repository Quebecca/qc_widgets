<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ListOfLastCreatedPagesProviderImp extends Provider
{
    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit, $orderType);
        $tsConfigLimit = intval($this->userTS['listOfLastCreatedPagesLimit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }

    }

    /**
     * @return string
     */
    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'lastCreatedPageInMyGroup');
    }

    /**
     *
     * @return array
     */
    public function getItems(): array
    {
        $membersUid = [];
        // get groups
        $groupsUid = explode(',', $GLOBALS['BE_USER']->user['usergroup']);
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

        // return the data from the database
        $result = $this->renderData($membersUid);

        // formatting data for date values
        $data = [];
        foreach ($result as $item){
            $item['crdate'] = date("Y-m-d H:i:s", $item['crdate']);
            $item['tstamp'] = date("Y-m-d H:i:s", $item['tstamp']);
            // verify if the page is expired
            $item['expired']  = $item['endtime'] !== 0 ? $item['endtime'] < time() ? 1 : 0 : 0;
            $data[]  = $item;
        }
        return $data;

    }

    /**
     * this function returns the query results
     * @param array $membersUid
     * @return array
     */
    public function renderData(array $membersUid) : array {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table)->createQueryBuilder();
        // the hidden pages or disabled can't be rendered with query builder restrictions
        $queryBuilder
            ->getRestrictions()
            ->removeAll();

        return $queryBuilder
            ->select('uid', 'title', 'crdate', 'tstamp', 'slug', 'hidden', 'endtime')
            ->from($this->table)
            ->where(
                $queryBuilder->expr()->in('cruser_id', $membersUid)
            )
            ->orderBy($this->orderField, 'DESC')
            ->setMaxResults($this->limit)
            ->execute()
            ->fetchAll();
    }
}