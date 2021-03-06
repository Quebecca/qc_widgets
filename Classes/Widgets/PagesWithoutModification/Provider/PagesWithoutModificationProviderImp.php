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


use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;

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
     */
    public function getItems(): array
    {
        // convert the number of months to seconds
        $sinceDate =  time() - $this->numberOfMonths * (29*24*60*60) ;
        $queryBuilder = $this->generateQueryBuilder($this->table);
        // if the tstamp is equal '0', we render the tt_contents created before the specified date
        $constraints  = [
            $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], \PDO::PARAM_INT)),
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lt('tstamp',$queryBuilder->createNamedParameter($sinceDate, \PDO::PARAM_INT)),
                    $queryBuilder->expr()->gt('tstamp',0),
                ),
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->lt('crdate',$queryBuilder->createNamedParameter($sinceDate,\PDO::PARAM_INT)),
                    $queryBuilder->expr()->eq('tstamp',0),
                ),
            ),

        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

}