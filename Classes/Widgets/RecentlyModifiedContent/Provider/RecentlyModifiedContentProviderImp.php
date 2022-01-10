<?php
namespace Qc\QcWidgets\Widgets\RecentlyModifiedContent\Provider;

use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Workspaces\Service\WorkspaceService;

class RecentlyModifiedContentProviderImp extends Provider
{
    /**
     * Overriding the LONG_FILE attribute
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/RecentlyModifiedContent/locallang.xlf:';


    /**
     * @var WorkspaceService
     */
    protected WorkspaceService $workspaceService;

    /**
     * @var PageRepository
     */
    protected $pagesRepository;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType,
        WorkspaceService $workspaceService = null
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        $this->pagesRepository = $pagesRepository ?? GeneralUtility::makeInstance(PageRepository::class);
        $this->setWidgetTitle($this->localizationUtility->translate(Self::LANG_FILE . 'recentlyModifiedContent'));
        $this->workspaceService = $workspaceService ?? GeneralUtility::makeInstance(WorkspaceService::class);
        // get the limit value from the tsconfig
        $tsConfigLimit = intval($this->userTS['qcWidgets.']['recentlyModifiedContent.']['limit']);
        if($tsConfigLimit && $tsConfigLimit > 0){
            $this->limit = $tsConfigLimit;
        }
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $data =  $this->renderData();
        return $this->dataMap($data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function dataMap(array $data){
        $result = [];
        foreach ($data as $item){
            if($item['header'] === ''){
                if(strlen($item['bodytext']) < 50 ){
                    $item['bodytext'] = substr($item['bodytext'],0, 50);
                }
                else{
                    $item['bodytext'] = substr($item['bodytext'],0, 50) . '...';
                }
            }
            $status = $this->getItemStatus($item['starttime'], $item['endtime']);
            $item['status'] = $status['status'];
            $item['statusMessage'] = $status['statusMessage'];
            $result [] = [
                'uid' => $item['uid'],
                'cType' => $item['cType'],
                'pid' => $item['pid'],
                'pageTitle' => $this->pagesRepository->getPage($item['pid'])['title'],
                'header' => $item['header'],
                'bodytext' => $item['bodytext'],
                'tstamp' =>  date("Y-m-d H:i:s", $item['tstamp']),
                'status' => $item['status'],
                'statusMessage' => $item['statusMessage']
            ];
        }
        return $result;
    }


    /**
     * @return array
     *
     */
    public function renderData() : array {
        $queryBuilder = $this->generateQueryBuilder($this->table);
        $queryBuilder
            ->getRestrictions()
            ->removeAll();
        $result = $queryBuilder
            ->select('uid', 'cType', 'pid', 'starttime', 'endtime', 'header', 'bodytext',  'tstamp')
            ->from('tt_content')
            ->orderBy('tstamp', 'DESC')
            ->setMaxResults(8)
            ->where(
                $queryBuilder->expr()->eq('cruser_id', $queryBuilder->createNamedParameter($GLOBALS['BE_USER']->user['uid']))
            )
            ->execute()
            ->fetchAll();
        return $result;
    }

}