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

use Doctrine\DBAL\Connection as ConnectionAlias;
use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\ListOfPagesProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LastCreatedPagesProviderImp extends ListOfPagesProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/LastCreatedPages/locallang.xlf:';

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit, $orderType);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'lastCreatedPageInMyGroup'));
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['lastCreatedPages.']['limit']);
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
        // return results
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $constraints = [];
        if(!empty($membersUid)){
            $constraints = [
                $queryBuilder->expr()->in('cruser_id', $queryBuilder->createNamedParameter($membersUid,  ConnectionAlias::PARAM_INT_ARRAY))
            ];
        }
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);

    }


}