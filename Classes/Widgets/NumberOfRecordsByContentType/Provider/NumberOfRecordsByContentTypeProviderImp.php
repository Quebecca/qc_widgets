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

        $tables = ['pages', 'tt_content'];
        $data = [];
        foreach ($tables as $table){
            $data [] = $this->renderData($table);
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function renderData(string $tableName): array
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
            ->setMaxResults(8)
            ->where(
                $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid'],\PDO::PARAM_INT))
            )
            ->execute()
            ->fetchAllAssociative();
    }
}