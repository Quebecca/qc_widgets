<?php
namespace Qc\QcWidgets\Widgets\NumberOfRecordsByContentType\Provider;

use Doctrine\DBAL\Driver\Exception;
use Qc\QcWidgets\Widgets\Provider;

class NumberOfRecordsByContentTypeProviderImp extends Provider
{

    /**
     * @throws Exception
     */
    public function getItems(): array
    {
        // Filtrer par periode
        // get tsconfig options
        $tablesName = [];
        $tsConfig = $this->getBackendUser()->getTSConfig()['mod.']['qcWidgets.']['numberOfRecordsByType.'];
        $tsTablesName = explode(',',$tsConfig['fromTable']);
        foreach ($tsTablesName as $tableName){
            $tablesName[] = str_replace(' ','', $tableName);
        }
        $totalRecordsOption = intval($tsConfig['totalRecords']);
        $totalNewLast24hOption = intval($tsConfig['totalNewLast24h']);
        $totalNewLastweekOption = intval($tsConfig['totalNewLastweek']);



        $data = [];
        foreach ($tablesName as $table){
            $data [$table] = $this->renderData($table);
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function renderData(string $tableName)
    {
        // La periode
        // L'utilisateur TS Config, Admin ??
        // Calculer la periode
        // Tester si la table ne contient pas le champs crdate

        $queryBuilder = $this->generateQueryBuilder($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        return $queryBuilder
            ->count('uid')
            ->from($tableName)
            ->execute()
            ->fetchAssociative()['COUNT(`uid`)'];
    }
}