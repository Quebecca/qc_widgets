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

namespace Qc\QcWidgets\Widgets\LastModifiedPages\Provider;


use Doctrine\DBAL\Connection as ConnectionAlias;
use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;

class LastModifiedPagesProviderImp extends ListOfPagesProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/LastModifiedPages/locallang.xlf:';

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'myLastPages'));
        // control the limit value, if the user has already specified a value for limiting the results
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['lastModifiedPages.']['limit']);
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
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $historyQueryBuilder = $this->generateQueryBuilder("sys_history");
        $historyConstraints = [
            $historyQueryBuilder->expr()->and(
                $historyQueryBuilder->expr()->eq('tablename', $historyQueryBuilder->createNamedParameter("pages")),
                $historyQueryBuilder->expr()->eq('userid', $historyQueryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], \PDO::PARAM_INT))
            )
        ];
        $results = $this->getRecordHistoryByUser($historyQueryBuilder, $historyConstraints, $this->limit);
        $pagesUids = [];
        foreach ($results as $result){
            $pagesUids[] = $result['recuid'];
        }

        $constraints = [
            $queryBuilder->expr()->and(
                 $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($pagesUids,  ConnectionAlias::PARAM_INT_ARRAY)),
                 $queryBuilder->expr()->eq('t3ver_wsid', 0),
                 $queryBuilder->expr()->eq('deleted', 0)
             )
        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

}