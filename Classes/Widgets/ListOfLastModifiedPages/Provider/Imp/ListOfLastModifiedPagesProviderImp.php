<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\Imp;


use Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\ListOfLastModifiedPagesProvider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ListOfLastModifiedPagesProviderImp implements ListOfLastModifiedPagesProvider
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
    )
    {
        $this->localizationUtility = $localizationUtility ?? GeneralUtility::makeInstance(LocalizationUtility::class);
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
    }

    public function getWidgetTitle() : string {
        return $this->localizationUtility->translate(Self::LANG_FILE . 'myLastPages');
    }

    public function getTable(): string
    {
        return $this->table;
    }

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
            ->setMaxResults($this->limit)
            ->execute()
            ->fetchAll();

    }
}