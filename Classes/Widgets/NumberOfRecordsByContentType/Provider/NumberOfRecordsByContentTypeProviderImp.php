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

namespace Qc\QcWidgets\Widgets\NumberOfRecordsByContentType\Provider;

use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
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
    protected int $totalRecordsByNumberOfDays = 0;
    /**
     * @var array
     */
    protected array $tablesConstraints = [];


    public function __construct(string $table, string $orderField, int $limit, string $orderType, LocalizationUtility $localizationUtility = null)
    {
        parent::__construct($table, $orderField, $limit, $orderType, $localizationUtility);
        $this->setWidgetTitle($this->localizationUtility->translate(self::LANG_FILE.'numberOfRecordsByContentType'));
        $last24h =strtotime(date('Y-m-d', strtotime('-1 day')));
        $this->totalRecordsByNumberOfDays = $this->getTsConfig('columns','totalRecordsByNumberOfDays')
            ? intval($this->getTsConfig('columns','totalRecordsByNumberOfDays'))
            : self::NUMBER_OF_DAYS;
        $numberOfDaysSec = strtotime("-$this->totalRecordsByNumberOfDays day");
        // the last element on each array is used to define if the constraint is enabled (1) or disabled (0)
        $this->columns = [
            'totalRecords' => [
                0 => ['totalRecords','true', 1]
            ],
            'totalRecordsForTheLast24h' => [
                0 => ['crdate', " crdate > $last24h", 1],
                1 => ['createdon', " createdon > $last24h", 1],
            ],
            'totalRecordsByNumberOfDays' => [
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
     * @throws Exception|\Doctrine\DBAL\Exception
     */
    public function getItems(): array
    {
        $tables = GeneralUtility::trimExplode(',',$this->getTsConfig('','fromTable'), true);
        $this->getEnabledColumns($tables);
        $data = [];
        foreach ($this->tablesConstraints as $table => $columnsOptions){
            foreach ($columnsOptions as $option => $constraint){
                if($constraint[2] == 1 || ($option == 'totalRecordsByNumberOfDays' && $constraint[2] >= 1)){
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function getEnabledColumns(array $tables){
        foreach ($tables as $table){
            if($this->checkIfTableExits($table)){
                foreach ($this->columns as $option => $constraints){
                    foreach ($constraints as $constraint){
                        if(is_array($constraint)){
                            $userOption = $this->getTsConfig('columns',$option) != null ? intval($this->getTsConfig('columns',$option)) : $constraint[2];
                            $checkColumn = $this->checkIfColumnExists($table, $constraint[0]);
                            if($constraint[0] == 'totalRecords')
                                $checkColumn = true;
                            if($userOption && $checkColumn){
                                if($userOption == 1 || $userOption == 0 || ($userOption > 0 && $option == 'totalRecordsByNumberOfDays')){
                                    $this->columns[$option][2] = $userOption;
                                    $this->tablesConstraints[$table][$option] = $constraint;
                                }
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAdditionalWhereClause(string $tableName): string
    {
        $whereClaue = '';
        foreach ($this->constraints as $option => $constraints){
            foreach ($constraints as  $constraint){
                $userOption = $this->getTsConfig('filter', $option) != null ?  intval($this->getTsConfig('filter',$option)) : $constraint[2];
                $checkColumn = $this->checkIfColumnExists($tableName, $constraint[0]);
                if($checkColumn && $userOption == 1){
                    $whereClaue .= $constraint[1];
                    break;
                }
            }
        }
        if($this->checkIfColumnExists($tableName, 'deleted'))
            $whereClaue .= ' AND deleted = 0';
        return $whereClaue;
    }

    /**
     * This function is used to check if column exists in table
     * @param $tableName
     * @param $column
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function checkIfColumnExists( $tableName,  $column) : bool{
        return in_array($column, array_keys(
            $this->getSchemaManager($tableName)->listTableColumns($tableName)
        ));
    }

    /**
     * This function checks of the table exists
     * @param $tableName
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function checkIfTableExits($tableName) : bool{
        return $this->getSchemaManager($tableName)->tablesExist([$tableName]);
    }

    /**
     * This function is used to return the SchemaManager
     * @param string $tableName
     * @return AbstractSchemaManager|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function getSchemaManager(string $tableName): ?AbstractSchemaManager
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->getSchemaManager();
    }

    /**
     * This function is used to get tsconfig option
     * @param string $optionType
     * @param string $tsConfigName
     * @return mixed
     */
    public function getTsConfig(string $optionType, string $tsConfigName){
        $tsconfig = $this->getBackendUser()->getTSConfig()['mod.']['qcWidgets.']['numberOfRecordsByType.'];
        return $optionType != '' ?
            $tsconfig[$optionType.'.'][$tsConfigName]
           : $tsconfig[$tsConfigName];
    }

    /**
     * This function is used to render data based on the passed constraint
     * @param string $tableName
     * @param string $constraint
     * @return mixed
     * @throws Exception|\Doctrine\DBAL\Exception
     */
    public function renderData(string $tableName, string $constraint)
    {
        $queryBuilder = $this->generateQueryBuilder($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        return  $queryBuilder
            ->count('*')
            ->from($tableName)->where($constraint)->executeQuery()
            ->fetchOne();
    }

    /**
     * @return int
     */
    public function getTotalRecordsByNumberOfDays(): int
    {
        return $this->totalRecordsByNumberOfDays;
    }
}