<?php
namespace Qc\QcWidgets\Widgets\NumberOfRecordsByContentType\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class NumberOfRecordsByContentTypeProviderImp extends Provider
{
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/NumberOfRecordsByContentType/locallang.xlf:';
    /**
     * @var array|string[]
     */
    protected array $constraints = [];

    public function __construct(string $table, string $orderField, int $limit, string $orderType, LocalizationUtility $localizationUtility = null)
    {
        parent::__construct($table, $orderField, $limit, $orderType, $localizationUtility);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'numberOfRecordsByContentType'));
        $numberOfDays = strtotime(date('Y-m-d'))  - 24*60*60*$this->getTsConfig('numberOfDays');
        $last24h = strtotime(date('Y-m-d')) - 24*60*60;
        $this->constraints = [
            'totalRecords' => 'true',
            'totalNewLast24h' => ' crdate >'.$last24h,
            'numberOfDays' => ' crdate > ' .$numberOfDays,
            'excludeDisabledItems' => ' AND disabled = 0',
            'excludeHiddenItems' => ' AND hidden = 0'
        ];
    }

    /**
     * This function is used to return data to the widget
     * @throws Exception
     */
    public function getItems(): array
    {
        $tablesName = [];
        $tsTablesName = explode(',',$this->getTsConfig('fromTable'));
        foreach ($tsTablesName as $tableName){
            $tablesName[] = str_replace(' ','', $tableName);
        }

        $constraints = $this->getEnabledConstraints();
        $data = [];
        foreach ($tablesName as $table){
            foreach ($constraints as $option => $constraint){
                if($option != 'excludeDisabledItems' && $option != 'excludeHiddenItems'){
                    if(
                        in_array('excludeHiddenItems', array_keys($constraints))
                        && $this->checkColumnExistence($table, 'hidden')
                    )
                    {
                        $constraint .= $this->constraints['excludeHiddenItems'];
                    }
                    if(
                        in_array('excludeDisabledItems', array_keys($constraints))
                        && $this->checkColumnExistence($table, 'disabled')
                    ) {
                        $constraint .= $this->constraints['excludeDisabledItems'];
                    }
                    $constraint .= ' AND deleted = 0';
                    $data [$table][$option] = $this->renderData($table, $constraint);
                }
            }
        }
        return $data;
    }

    /**
     * Check if the column exists in the DB to avoid DB errors
     * @param string $tableName
     * @param string $column
     * @return bool
     */
    public function checkColumnExistence(string $tableName, string $column): bool
    {
        return in_array($column, array_keys($GLOBALS['TCA'][$tableName]['columns']));

    }

    /**
     * This function is used to return the enabled tsconfig options
     * @return array
     */
    public function getEnabledConstraints(): array
    {
        $enabledConstraints = [];
        // todo : verify if the column is present in the table in DB
        foreach ($this->constraints as $option => $constraint){
            if(
                intval($this->getTsConfig($option)) == 1
                || (intval($this->getTsConfig($option)) >= 1 && $option == 'numberOfDays')
            ){
                $enabledConstraints[$option] = $constraint;
            }
        }
        return $enabledConstraints;
    }

    /**
     * This function is used to get tsconfig option
     * @param string $tsConfigName
     * @return mixed
     */
    public function getTsConfig(string $tsConfigName){
        return  $this->getBackendUser()->getTSConfig()['mod.']['qcWidgets.']['numberOfRecordsByType.'][$tsConfigName];
    }

    /**
     * This function is used to render data based on the passed constraint
     * @param string $tableName
     * @param string $constraint
     * @return mixed
     * @throws Exception
     */
    public function renderData(string $tableName, string $constraint)
    {
        $queryBuilder = $this->generateQueryBuilder($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        return $queryBuilder
            ->count('uid')
            ->from($tableName)
            ->where(
                $constraint
            )
            ->execute()
            ->fetchAssociative()['COUNT(`uid`)'];
    }
}