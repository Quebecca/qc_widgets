<?php
namespace Qc\QcWidgets\Widgets\Provider\Imp;

use Qc\QcWidgets\Widgets\Provider\ListOfMembersProvider;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ListOfMembersProviderImp implements ListOfMembersProvider
{
    /**
     * @var string
     */
    const LANG_FILE = 'LLL:EXT:qc_widgets/Resources/Private/Language/Module/ListOfMembers/locallang.xlf:';

    /**
     * @var BackendUserRepository
     */
    protected $backendUserRepository;
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
    /**
     * @var LocalizationUtility
     */
    private $localizationUtility;
    /**
     * @var BackendUserGroupRepository
     */
    private $backendUserGroupRepository;

    public function __construct(
        string $table,
        string $orderField,
        string $limit,
        string $order,
        LocalizationUtility $localizationUtility = null,
        BackendUserRepository  $backendUserRepository = null
    )
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
        $this->order = $order;
        $this->localizationUtility = $localizationUtility ?? GeneralUtility::makeInstance(LocalizationUtility::class);

        $this->backendUserGroupRepository = $backendUserGroupRepository ?? GeneralUtility::makeInstance(BackendUserGroupRepository::class);
        $this->backendUserRepository = $backendUserRepository ?? GeneralUtility::makeInstance(BackendUserRepository::class);

        //Initialize Repository Backend user
        /*$objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $persistenceManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager');
        $this->backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class, $objectManager);
        $this->backendUserRepository->injectPersistenceManager($persistenceManager);*/
    }

    public function getTable(): string
    {
        return $this->table;
    }
    // $this->setWorkspace($this->user['workspace_id']);

    public function getItems(): array
    {

/*        // get the uid of the connected user
        $userUid = $this->getBackendUser()->user['uid'];
        // get his group uids
        $queryBuilder1 = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $userGroupUids = $queryBuilder1->select('usergroup')
            ->from($this->table)
            ->where(
                $queryBuilder1->expr()->eq('uid', $queryBuilder1->createNamedParameter($userUid, \PDO::PARAM_INT))
            )
            ->execute()
            ->fetch();
        // use the groups uids to render other users
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($this->table);
        return $queryBuilder
            ->select('username', 'realName', 'email', 'tstamp')
            ->from($this->table)
            ->where(
                $queryBuilder1->expr()->inSet('usergroup', $userGroupUids)
            )
            ->setMaxResults($this->limit)
            ->addOrderBy($this->orderField, $this->order)
            ->execute()
            ->fetchAll();
*/
        // Methode 1
        /*$groups = $GLOBALS['BE_USER']->user['usergroup'];
        $users =  \TYPO3\CMS\Backend\Utility\BackendUtility::getUserNames('username,realName, email,lastlogin', "AND FIND_IN_SET(usergroup, '$groups') AND disable = 0");
        return $users;*/

        // Methode 2
        $users = [];
        $groupsUid = explode(',', $GLOBALS['BE_USER']->user['usergroup']);
        foreach ($groupsUid as $guid) {
            $demand = new Demand();
            $demand->setBackendUserGroup((int)$guid);
            $groupName = $this->backendUserGroupRepository->findByUid($guid);
            $users[] = [
                "group" => $groupName->getTitle(),
                "members" => $this->formattingUserItems($this->backendUserRepository->findDemanded($demand))
            ];
        }
        return $users;
    }

    public function formattingUserItems( $usersData): array
    {
        $listOfMembers = [];
        foreach ($usersData as $item) {
            // check for non provided values
            if ($item->getEmail() === '') {
                $item->setEmail(
                    $this->localizationUtility->translate(Self::LANG_FILE . 'emailNotProvided')
                );
            }
            if ($item->getRealName() === '') {
                $item->setRealName(
                    $this->localizationUtility->translate(Self::LANG_FILE . 'realNameNotProvided')
                );
            }
            array_push($listOfMembers, [
                 $item->getUsername(),
                $item->getEmail(),
                 $item->getRealName(),
                $item->getLastLoginDateAndTime()
            ]);
            /*$listOfMembers[] = [
                'username' => $item->getUsername(),
                'email' => $item->getEmail(),
                'realName' => $item->getRealName(),
                'lastlogin' => $item->getLastLoginDateAndTime()
            ];*/
        }
        return $listOfMembers;
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}