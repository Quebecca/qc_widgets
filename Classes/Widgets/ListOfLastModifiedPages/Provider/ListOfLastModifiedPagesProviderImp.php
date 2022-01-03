<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider;


use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ListOfLastModifiedPagesProviderImp extends Provider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:';

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        // control the limit value, if the user has already specified a value for limiting the results
        $tsConfigLimit = intval($this->userTS['listOfLastModifiedPagesLimit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * @return string
     */
    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'myLastPages');
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        /*
         * The method TYPO3\CMS\Backend\Utility\BackendUtility::getRecordsByField() has been deprecated and should not be used any longer.
         * https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/8.7/Deprecation-79122-DeprecateBackendUtilitygetRecordsByField.html
         */

        // Query builder
        $result = $this->renderData();
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
     * this function return the query for pages records
     * @return array
     */
    public function renderData() : array {

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table)->createQueryBuilder();
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
         return $queryBuilder
            ->select('uid', 'title', 'crdate', 'tstamp', 'slug', 'hidden', 'endtime')
            ->from($this->table)
            ->where(
                $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid']))
            )
            ->orderBy($this->orderField, 'DESC')
            ->setMaxResults(intval($this->limit))
            ->execute()
            ->fetchAll();

    }
}