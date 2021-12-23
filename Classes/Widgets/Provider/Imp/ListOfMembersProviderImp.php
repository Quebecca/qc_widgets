<?php
namespace Qc\QcWidgets\Widgets\Provider\Imp;

use Qc\QcWidgets\Widgets\Provider\ListOfMembersProvider;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ListOfMembersProviderImp implements ListOfMembersProvider
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * @var string
     */
    protected string $orderField = '';

    /**
     * @var string
     */
    protected string $limit = '';

    /**
     * @var string
     */
    protected string $order= '';

    public function __construct(string $table, string $orderField, string $limit, string $order)
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
        $this->order = $order;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getItems(): array
    {
        // $this->setWorkspace($this->user['workspace_id']);
        $userUid = $this->getBackendUser()->user['uid'];
        $userGroupUids = [];

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $userGroupUids = $queryBuilder->select('usergroup')
        ->from($this->table)
        ->where(
            $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($userUid, \PDO::PARAM_INT))
        )
        ->execute()
        ->fetch();

        //$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        return $queryBuilder
            ->select('username', 'realName', 'email', 'tstamp')
            ->from($this->table)
            ->join(
                $this->table,
                'be_groups',
                'bg',
                      $queryBuilder->expr()->eq('bg.uid', '3')
            )
            ->setMaxResults($this->limit)
            ->addOrderBy($this->orderField, $this->order)
            ->execute()
            ->fetchAll();
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}