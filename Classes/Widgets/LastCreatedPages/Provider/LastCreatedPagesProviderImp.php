<?php
namespace Qc\QcWidgets\Widgets\LastCreatedPages\Provider;

use Qc\QcWidgets\Widgets\ListOfPagesProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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
        $this->setWidgetTitle($this->localizationUtility->translate(SELF::LANG_FILE.'lastCreatedPageInMyGroup'));
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['lastCreatedPages.']['limit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }

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
        // return results
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $constraints = [
            $queryBuilder->expr()->in('cruser_id', $membersUid)
        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);

    }


}