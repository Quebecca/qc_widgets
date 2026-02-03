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

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;
use TYPO3\CMS\Core\Database\Connection;

class LastModifiedPagesProviderImp extends ListOfPagesProvider
{

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
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
                $historyQueryBuilder->expr()->eq('userid', $historyQueryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], Connection::PARAM_INT))
            )
        ];
        $results = $this->getRecordHistoryByUser($historyQueryBuilder, $historyConstraints, $this->limit);
        $pagesUids = [];
        foreach ($results as $result){
            $pagesUids[] = $result['recuid'];
        }

        $constraints = [
            $queryBuilder->expr()->and(
                 $queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($pagesUids,  Connection::PARAM_INT_ARRAY)),
                 $queryBuilder->expr()->eq('t3ver_wsid', 0),
                 $queryBuilder->expr()->eq('deleted', 0)
             )
        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

}