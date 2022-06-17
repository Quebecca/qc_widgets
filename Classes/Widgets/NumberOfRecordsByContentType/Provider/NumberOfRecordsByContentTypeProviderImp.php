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
            'totalRecords' => [
                0 => ['','true', true]
            ],
            'totalNewLast24h' => [
                0 => ['crdate', " crdate > $last24h", false]
            ],
            'numberOfDays' => [
                0 => ['crdate'," crdate >  $numberOfDays",false]
            ],
            'excludeDisabledItems' => [
                0 => ['disabled', ' AND disabled = 0', false],
                1 => ['disable', ' AND disable = 0', false],
            ],
            'excludeHiddenItems' => [
                0 => ['hidden',' AND hidden = 0', false]
            ],
            'excludDeleted' => [
                0 => ['deleted',' AND deleted = 0', false]
            ],
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

        $data = [];
        $enabledConstraints = $this->getEnabledConstraints($tablesName);
        foreach ($enabledConstraints as $table => $tableConstraints){

            foreach (['totalRecords', 'totalNewLast24h', 'numberOfDays'] as $recordType){

                if($tableConstraints[$recordType] != null && $tableConstraints[$recordType][2]){
                    $whereClause  = $tableConstraints[$recordType][1];
                    foreach ([
                                 'excludDeleted',
                                 'excludeHiddenItems',
                                 'excludeDisabledItems'
                             ] as $constraintItem){


                        if($tableConstraints[$constraintItem][2] == true){
                            $whereClause .= ' '. $tableConstraints[$constraintItem][1];

                        }
                    }
                    $data[$table][$recordType] = $this->renderData($table, $whereClause);
                }
            }
        }

        return $data;
    }

    /**
     * This function is used to return the enabled tsconfig options
     * @param $tablesName
     * @return array
     */
    public function getEnabledConstraints( $tablesName): array
    {

        $enabledConstraints = [];
        foreach ($tablesName as $table){
            if($this->checkIfTableExits($table)){
                foreach ($this->constraints as $option => $constraint){
                    if(
                        intval($this->getTsConfig($option)) == 1
                        || (intval($this->getTsConfig($option)) >= 1 && $option == 'numberOfDays')
                        || $option == 'excludDeleted'
                    ){
                        foreach ($constraint as $item){
                            $enableColumn = in_array($item[0],array_keys( $this->getTableColumns($table)));
                            if($enableColumn || $option == 'totalRecords'){
                                $enabledConstraints[$table][$option] = $item;
                                $enabledConstraints[$table][$option][2] = true;
                            }
                        }

                    }
                }
            }
        }

        return $enabledConstraints;
    }

    public function checkIfTableExits($tableName) : bool{
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable($tableName)
            ->getSchemaManager()
            ->tablesExist([$tableName]);
    }

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