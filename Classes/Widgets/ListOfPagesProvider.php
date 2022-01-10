<?php

namespace Qc\QcWidgets\Widgets;

use TYPO3\CMS\Core\Database\Query\QueryBuilder;

abstract class ListOfPagesProvider extends Provider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:';

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
            ->select('uid', 'title', 'crdate', 'tstamp', 'slug', 'hidden', 'endtime', 'starttime')
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
            // verify if the page is expired
            $item['expired']  = $item['endtime'] !== 0 ? $item['endtime'] < time() ? 1 : 0 : 0;
            if($item['expired'] == 1){
                $numberOfDays = round((time() - $item['endtime']) / (60*60*24));
                $item['expiredMessage'] =  $this->localizationUtility->translate(Self::LANG_FILE . 'stop') . ' '. date('d-m-y', $item['endtime']) . " ( $numberOfDays ".  $this->localizationUtility->translate(Self::LANG_FILE . 'days'). " )";
            }
            // verify if the page is not available yet
            $item['available'] = $item['starttime'] !== 0 ? $item['starttime'] < time() ? 1 : 0 : 33;
            if($item['available'] == 0){
                $numberOfDays = round(($item['starttime'] - time()) / (60*60*24));
                $item['availableMessage'] =  $this->localizationUtility->translate(Self::LANG_FILE . 'start') . ' '. date('d-m-y', $item['starttime']) . " ( $numberOfDays ".  $this->localizationUtility->translate(Self::LANG_FILE . 'days'). " )";
            }
            if($item['hidden'] == 1){
                $item['hiddenMessage'] = $this->localizationUtility->translate(Self::LANG_FILE . 'hidden');
            }
            $data[]  = $item;
        }
        return $data;
    }
}