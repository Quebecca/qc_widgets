<?php
namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

abstract class Provider
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
        $this->userTS = $this->getBackendUser()->getTSConfig()['mod.']['qcwidgets.'];
    }


    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    /*
     * this function returns the widget title
     * @return string
     */
    public abstract function getWidgetTitle() : string;

    /*
     * This function returns the array of pages records after rendering results from the database
     */
    public abstract function getItems();

}