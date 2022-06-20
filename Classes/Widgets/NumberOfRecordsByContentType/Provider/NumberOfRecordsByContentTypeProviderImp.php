<?php
namespace Qc\QcWidgets\Widgets\NumberOfRecordsByContentType\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class NumberOfRecordsByContentTypeProviderImp extends Provider
{
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/NumberOfRecordsByContentType/locallang.xlf:';
    const NUMBER_OF_DAYS = 365;
    /**
     * @var array|\array[][]
     */
    protected array $columns = [];
    /**
     * @var array|\array[][]
     */
    protected array $constraints = [];
    /**
     * @var int
     */
    protected int $numberOfDays = 0;
    /**
     * @var array
     */
    protected array $tablesConstraints = [];


    public function __construct(string $table, string $orderField, int $limit, string $orderType, LocalizationUtility $localizationUtility = null)
    {
        parent::__construct($table, $orderField, $limit, $orderType, $localizationUtility);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'numberOfRecordsByContentType'));
        $last24h = strtotime(date('Y-m-d')) - 24*60*60;
        if($this->getTsConfig('numberOfDays')){
            $this->numberOfDays = intval($this->getTsConfig('numberOfDays')) >= 0 ? intval($this->getTsConfig('numberOfDays')) : self::NUMBER_OF_DAYS;
        }
        $numberOfDaysSec = strtotime(date('Y-m-d'))  - 24*60*60*$this->numberOfDays;
        $this->columns = [
            'totalRecords' => [
                0 => ['totalRecords','true', 1]
            ],
            'totalNewLast24h' => [
                0 => ['crdate', " crdate > $last24h", 1],
                1 => ['createdon', " createdon > $last24h", 1],
            ],
            'numberOfDays' => [
                0 => ['crdate'," crdate >  $numberOfDaysSec",$numberOfDaysSec],
                1 => ['createdon'," createdon >  $numberOfDaysSec",$numberOfDaysSec],
            ]
        ];

        $this->constraints = [
            'excludeDisabledItems' => [
                0 => ['disabled', ' AND disabled = 0', 1],
                1 => ['disable', ' AND disable = 0', 1],
            ],
            'excludeHiddenItems' => [
                0 => ['hidden',' AND hidden = 0', 1]
            ]
        ];
    }

    /**
     * This function is used to return data to the widget
     * @throws Exception
     */
    public function getItems(): array
    {
        $tables = GeneralUtility::trimExplode(',',$this->getTsConfig('fromTable'), true);
        $this->getEnabledColumns($tables);
        $data = [];
        foreach ($this->tablesConstraints as $table => $columnsOptions){
            foreach ($columnsOptions as $option => $constraint){
                if($constraint[2] == 1 || ($option == 'numberOfDays' && $constraint[2] >= 1)){
                    $whereClause = $constraint[1];
                    $whereClause .= $this->getAdditionalWhereClause($table);
                    $data[$table][$option] = $this->renderData($table, $whereClause);
                }
            }
        }
        return $data;
    }

    /**
     * This function will be used to return the available column for the selected table
     * @param array $tables
     */
    public function getEnabledColumns(array $tables){
        foreach ($tables as $table){
            if($this->checkIfTableExits($table)){
                foreach ($this->columns as $option => $constraints){
                    foreach ($constraints as $constraint){
                        $userOption = intval($this->getTsConfig($option));
                        $test = in_array($constraint[0], array_keys($this->getTableColumns($table)));
                        if($constraint[0] == 'totalRecords')
                            $test = true;
                        if($userOption && $test){
                            if($userOption == 1 || $userOption == 0 || ($userOption > 0 && $option == 'numberOfDays')){
                                $this->columns[$option][2] = $userOption;
                                $this->tablesConstraints[$table][$option] = $constraint;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * This function return the additional where clause for each table passed in parameter
     * @param string $tableName
     * @return string
     */
    public function getAdditionalWhereClause(string $tableName): string
    {
        $whereClaue = '';
        foreach ($this->constraints as $option => $constraints){
            $userOption = intval($this->getTsConfig($option));
            if($userOption && ($userOption == 0 || $userOption == 1)) {
                foreach ($constraints as  $constraint){
                    $test = in_array($constraint[0], array_keys($this->getTableColumns($tableName)));
                    if($test && $userOption == 1){
                        $whereClaue .= $constraint[1];
                        //$this->constraints[$option] = $constraint;
                    }
                }
            }
        }
        if(in_array('deleted', array_keys($this->getTableColumns($tableName))))
            $whereClaue .= ' AND deleted = 0';
        return $whereClaue;
    }


    /**
     * This function checks of the table exists
     * @param $tableName
     * @return bool
     */
    public function checkIfTableExits($tableName) : bool{
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->getSchemaManager()
            ->tablesExist([$tableName]);
    }

    /**
     * This function is used to check if the column is existe in the selected table
     * @param $tableName
     * @return array
     */
    public function getTableColumns($tableName): array
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->getSchemaManager()
            ->listTableColumns($tableName);
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
        // some tables does not contain uid column
        $column = array_keys($this->getTableColumns($tableName))[0];
        $queryBuilder = $this->generateQueryBuilder($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        return $queryBuilder
            ->count($column)
            ->from($tableName)
            ->where(
                $constraint
            )
            ->execute()
            ->fetchAssociative()['COUNT(`uid`)'];
    }

    /**
     * @return int
     */
    public function getNumberOfDays(): int
    {
        return $this->numberOfDays;
    }
}