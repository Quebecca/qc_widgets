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
namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

abstract class Provider
{

    /**
     * Overriding the LONG_FILE attribute
     * @var string
     */
    public const QC_LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:';

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
        protected string $table,
        protected string $orderField,
        protected int $limit,
        protected string $orderType,
        LocalizationUtility $localizationUtility = null
    ){
        $this->localizationUtility = $localizationUtility ?? GeneralUtility::makeInstance(LocalizationUtility::class);
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

    public function setWidgetTitle(string $widgetTitle){
        $this->widgetTitle = $widgetTitle;
    }

    /*
     * This function returns the array of records after rendering results from the database
     */
    public abstract function getItems();


    /**
     * This function returns the status of the stored element
     * @param $startTime
     * @param $endTime
     * @return string[]
     */
    public function getItemStatus($startTime, $endTime): array
    {
        $status = [];
         // expired, not available, available

        if($endTime !== 0 && $endTime < time()){
            $numberOfDays = round((time() - $endTime) / (60*60*24));
            return  [
                'status' => 'expired',
                'statusMessage' => $this->localizationUtility->translate(self::QC_LANG_FILE . 'stop') . ' '. date('d-m-y', $endTime)
                    . " ( $numberOfDays ".  $this->localizationUtility->translate(self::QC_LANG_FILE . 'days'). " )"
            ];

        }
        else if($startTime !== 0 && $startTime > time()) {
            $numberOfDays = round(($startTime - time()) / (60 * 60 * 24));
            return [
                'status' => 'notAvailable',
                'statusMessage' => $this->localizationUtility->translate(self::QC_LANG_FILE . 'start') . ' ' . date('d-m-y', $startTime)
                    . " ( $numberOfDays " . $this->localizationUtility->translate(self::QC_LANG_FILE . 'days') . " )"
            ];
        }
        return [
            'status' => 'available',
            'statusMessage' => ''
        ];
    }

}