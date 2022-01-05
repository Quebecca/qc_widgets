<?php

namespace Qc\QcWidgets\Widgets\LastModifiedPages\Provider;


use Qc\QcWidgets\Widgets\ListOfPagesProvider;

class LastModifiedPagesProviderImp extends ListOfPagesProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/locallang.xlf:';

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $this->widgetTitle = 'myLastPages';
        // control the limit value, if the user has already specified a value for limiting the results
        $tsConfigLimit = intval($this->userTS['listOfLastModifiedPagesLimit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        /*
         * The method TYPO3\CMS\Backend\Utility\BackendUtility::getRecordsByField() has been deprecated and should not be used any longer.
         * https://docs.typo3.org/c/typo3/cms-core/main/en-us/Changelog/8.7/Deprecation-79122-DeprecateBackendUtilitygetRecordsByField.html
         */
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $constraints = [
            $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid']))
        ];
        $result = $this->renderData($queryBuilder,$constraints);
        return $this->dataMap($result);
    }

}