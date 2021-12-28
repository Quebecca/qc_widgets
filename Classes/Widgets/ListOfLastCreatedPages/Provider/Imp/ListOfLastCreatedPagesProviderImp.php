<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\Imp;



use Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\ListOfLastCreatedPagesProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ListOfLastCreatedPagesProviderImp implements ListOfLastCreatedPagesProvider
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * @var string
     */
    protected string $orderField = '';

    /**
     * @var string
     */
    protected string $limit = '';

    public function __construct(string $table, string $orderField, string $limit)
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getItems(): array
    {
        $membersUid = [];
        // select the uid of the user
        $userUid =  $GLOBALS['BE_USER']->user['uid'];
        // get groups
        $groupsUid = explode(',', $GLOBALS['BE_USER']->user['usergroup']);
        // get uid of members
        foreach ($groupsUid as $groupUid){
            $data =  BackendUtility::getUserNames('uid', "AND usergroup LIKE  '%$groupUid%'  AND disable = 0");
            foreach($data as $key => $val){
                // prevent the duplicated users uid
                if(!in_array($key, $membersUid)){
                    $membersUid[] = $key;
                }
            }
        }
       $result = $this->renderData($membersUid);

        // formatting data

        $data = [];

        foreach ($result as $item){
            $item['crdate'] = date("Y-m-d H:i:s", $item['crdate']);
            $item['tstamp'] = date("Y-m-d H:i:s", $item['tstamp']);
            $data[]  = $item;
        }
        return $data;

    }

    public function renderData(array $membersUid) : array {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable('pages')->createQueryBuilder();
        return $queryBuilder
            ->select('uid', 'title', 'crdate', 'tstamp', 'slug')
            ->from('pages')
            ->where(
                $queryBuilder->expr()->in('cruser_id', $membersUid)
            )
            ->orderBy('crdate', 'DESC')
            ->setMaxResults($this->limit)
            ->execute()
            ->fetchAll();
    }
}