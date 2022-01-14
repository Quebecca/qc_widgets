<?php

namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

abstract class ListOfPagesProvider extends Provider
{
    /**
     * this function returns the query for pages records
     * @param QueryBuilder $queryBuilder
     * @param array $constraints
     * @return array
     */
    public function renderData(QueryBuilder  $queryBuilder, array $constraints) : array {
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        return $queryBuilder
            ->select('uid', 'title', 'crdate', 'tstamp', 'slug', 'hidden', 'endtime', 'starttime', 'doktype')
            ->from($this->table)
            ->where(
                ...$constraints
            )
            ->orderBy($this->orderField, $this->orderType)
            ->setMaxResults($this->limit)
            ->execute()
            ->fetchAll();
    }

    /**
     * This function returns the formatted items
     * @param array $result
     * @return array
     */
    public function dataMap(array $result): array {
        $data = [];
        foreach ($result as $item){
            $item['crdate'] = date("Y-m-d H:i:s", $item['crdate']);
            $item['tstamp'] = date("Y-m-d H:i:s", $item['tstamp']);
            $status = $this->getItemStatus($item['starttime'], $item['endtime']);
            $item['status'] = $status['status'];
            $item['statusMessage'] = $status['statusMessage'];
            if($item['hidden'] == 1){
                $item['hiddenMessage'] = $this->localizationUtility->translate(Self::QC_LANG_FILE . 'hidden');
            }
            $data[]  = $item;
        }
        return $data;
    }
}