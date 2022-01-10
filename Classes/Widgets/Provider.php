<?php
namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

abstract class Provider
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
    protected string $orderType = '';

    /**
     * @var int
     */
    protected int $limit = 0;

    /**
     * @var LocalizationUtility
     */
    protected $localizationUtility;

    /**
     * @var string
     */
    protected string $widgetTitle = '';

    /**
     * @var mixed
     */
    protected $userTS;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType,
        LocalizationUtility $localizationUtility = null
    ){
        $this->localizationUtility = $localizationUtility ?? GeneralUtility::makeInstance(LocalizationUtility::class);
        $this->table = $table;
        $this->orderField = $orderField;
        $this->orderType = $orderType;
        $this->limit = $limit;
        $this->initializeTsConfig();
    }

    /**
     * this function returns the specified values from the tsconfig
     */
    protected function initializeTsConfig(){
        /*Initialize the TsConfing mod of the current Backend user */
        $this->userTS = $this->getBackendUser()->getTSConfig()['mod.'];
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    protected function generateQueryBuilder(string $table): QueryBuilder
    {
        /**
         * @var ConnectionPool $connectionPool
         */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($table);
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /*
     * @return string
     */
    public function getWidgetTitle() : string {
        return $this->widgetTitle;
    }

    /**
     * @param string $widgetTitle
     */
    public function setWidgetTitle(string $widgetTitle){
        $this->widgetTitle = $widgetTitle;
    }


    /*
     * This function returns the array of records after rendering results from the database
     */
    public abstract function getItems();

}