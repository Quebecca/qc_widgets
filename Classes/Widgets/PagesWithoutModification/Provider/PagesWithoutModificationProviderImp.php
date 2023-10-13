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

namespace Qc\QcWidgets\Widgets\PagesWithoutModification\Provider;


use Doctrine\DBAL\Connection as ConnectionAlias;
use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\History\RecordHistoryStore;

class PagesWithoutModificationProviderImp extends ListOfPagesProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/PagesWithoutModification/locallang.xlf:';

    /**
     * @var int
     */
    protected int $numberOfMonths = 0;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        // get the limit value from the tsconfig
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['pagesWithoutModification.']['limit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
        // get the tsconfig value of the number of months
        $numberofMonthsTs = intval($this->userTS['qcWidgets.']['pagesWithoutModification.']['numberOfMonths']);
        if($numberofMonthsTs && $numberofMonthsTs > 0){
            $this->numberOfMonths = $numberofMonthsTs;
        }
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'pagesWitouhtModificationFor') . ' ' . strval($this->numberOfMonths) . ' ' . $this->localizationUtility->translate(self::LANG_FILE.'months'));

    }

    /**
     * This function returns the array of records after rendering results from the database
     * @return array
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getItems(): array
    {
        // convert the number of months to seconds
        $sinceDate =  time() - $this->numberOfMonths * (29*24*60*60) ;
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $historyQueryBuilder = $this->generateQueryBuilder("sys_history");

        $pagesBeforeXMonths = $this->getPagesBeforeXMonths($historyQueryBuilder, $sinceDate);
        $pagesAfterXMonths = $this->getPagesAfterXMonths($historyQueryBuilder, $sinceDate);
        $pagesUids = array_diff($pagesBeforeXMonths,$pagesAfterXMonths);

        $constraints  = [
             $queryBuilder->expr()->and(
                 $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($pagesUids,  ConnectionAlias::PARAM_INT_ARRAY)),
                 $queryBuilder->expr()->lt('crdate',$queryBuilder->createNamedParameter($sinceDate,\PDO::PARAM_INT)),
                 $queryBuilder->expr()->eq('t3ver_wsid', 0),
                 $queryBuilder->expr()->eq('deleted', 0)
             ),
        ];

        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

    /**
     * @param $sysHistoryQb
     * @param $constraints
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getHistoryOfPages($sysHistoryQb, $constraints) : array {
        $historyRecordConstraints = [
            $sysHistoryQb->expr()->eq('tablename', $sysHistoryQb->createNamedParameter("pages")),
            $sysHistoryQb->expr()->eq('userid', $sysHistoryQb->createNamedParameter($GLOBALS['BE_USER']->user['uid'], \PDO::PARAM_INT)),
            $sysHistoryQb->expr()->eq('actiontype', $sysHistoryQb->createNamedParameter(RecordHistoryStore::ACTION_MODIFY, Connection::PARAM_INT)),
            ...$constraints
            ];

        $results = $this->getRecordHistoryByUser($sysHistoryQb, $historyRecordConstraints);
        $pagesUids = [];
        foreach ($results as $result){
            $pagesUids[] = $result['recuid'];
        }
        return $pagesUids;

    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPagesBeforeXMonths(QueryBuilder $queryBuilder, $sinceDate): array
    {
        $historyBeforeSinceDate = [
            $queryBuilder->expr()->or(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->lt('tstamp',$queryBuilder->createNamedParameter($sinceDate, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->gt('tstamp',0)
                )
            )
        ];

        return $this->getHistoryOfPages( $queryBuilder, $historyBeforeSinceDate);

    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function getPagesAfterXMonths(QueryBuilder $queryBuilder, $sinceDate): array
    {
        $historyAfterSinceDate = [
            $queryBuilder->expr()->and(
                $queryBuilder->expr()->lt('tstamp',$queryBuilder->createNamedParameter(time(), \PDO::PARAM_INT)),
                $queryBuilder->expr()->gt('tstamp',$sinceDate)
            )
        ];
        return $this->getHistoryOfPages( $queryBuilder,$historyAfterSinceDate);
    }
}
