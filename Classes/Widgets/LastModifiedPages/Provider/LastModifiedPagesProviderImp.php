<?php

/***
 *
 * This file is part of Qc Widgets.
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
     */
    public function getItems(): array
    {
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $constraints = [
            $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'], \PDO::PARAM_INT))
        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

}