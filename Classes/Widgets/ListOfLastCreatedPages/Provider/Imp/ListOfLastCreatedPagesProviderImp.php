<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\Imp;

use Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider\ListOfLastCreatedPagesProvider;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ListOfLastCreatedPagesProviderImp implements ListOfLastCreatedPagesProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:';
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

    /**
     * @var LocalizationUtility
     */
    private $localizationUtility;

    public function __construct(
        string $table,
        string $orderField,
        string $limit,
        LocalizationUtility $localizationUtility = null
    ){
        $this->localizationUtility = $localizationUtility ?? GeneralUtility::makeInstance(LocalizationUtility::class);
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
    }

    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'lastCreatedPageInMyGroup');
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

        // formatting data for date value
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

    public function renderData(array $membersUid) : array {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($this->table)->createQueryBuilder();
        // the hidden pages or disabled can't be rendered with query builder restriction
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