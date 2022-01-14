<?php
namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider;

use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Entities\ListOfMemebers;
use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Entities\Member;
use Qc\QcWidgets\Widgets\Provider;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Backend\Utility\BackendUtility;

class ListOfMembersProviderImp extends Provider
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
     * @var BackendUserGroupRepository
     */
    private $backendUserGroupRepository;

    /**
     * @var int
     */
    private int $numberOfUsers = 0;

    public function __construct(
        string $table,
        string $orderField,
        int $limit,
        string $orderType,
        BackendUserRepository  $backendUserRepository = null
    )
    {
        parent::__construct($table,$orderField,$limit,$orderType);
        // check if the current user is admin
        if($GLOBALS['BE_USER']->isAdmin()){
            $this->setWidgetTitle($this->localizationUtility->translate(Self::LANG_FILE . 'listOfAdminsMembers'));
        }
        else{
            $this->setWidgetTitle($this->localizationUtility->translate(Self::LANG_FILE . 'listOfMyTeamsMembers'));
        }
        $this->backendUserGroupRepository = $backendUserGroupRepository ?? GeneralUtility::makeInstance(BackendUserGroupRepository::class);
        $this->backendUserRepository = $backendUserRepository ?? GeneralUtility::makeInstance(BackendUserRepository::class);
    }

    /**
     * @return ListOfMemebers
     */
    public function getItems(): ListOfMemebers
    {
        $users = [];
        $members = new ListOfMemebers();
        $members->setIsAdmin($GLOBALS['BE_USER']->isAdmin());

        if($GLOBALS['BE_USER']->isAdmin()){
            // if the current user is an admin, we return the list of admins
            $userData = $this->renderUsersData("AND ADMIN = 1 AND disable = 0");
            $users [] = [
                'groupName' => '',
                'users' => $userData
            ];
        }
        else{
            $queryBuilder =  $this->generateQueryBuilder('be_users');
            // if the current user is not an admin, we return the users in the same groups as the current user
            $groupsUid = explode(',', $GLOBALS['BE_USER']->user['usergroup']);
            $dontLookintoSubgroups = intval($this->userTS['qcWidgets.']['listOfmembers.']['dontLookintoSubgroups']);
            if($dontLookintoSubgroups == 1) {
                $subgroups = [] ;
                foreach ($groupsUid as $groupUid) {
                    $subgroups  = $this->getSubGroupsUid($groupUid);
                }
                $groupsUid = array_merge($subgroups, $groupsUid);
            }

            foreach ($groupsUid as $groupUid){
                $groupName = $this->backendUserGroupRepository->findByUid($groupUid);
                $users [] = [
                    'groupName' => $groupName !== null ? $groupName->getTitle() : '',
                   // 'users' => $this->renderUsersData("AND FIND_IN_SET('$groupUid', usergroup)  AND disable = 0")
                    'users' => $this->renderUsersData( 'AND '.
                        $queryBuilder->expr()
                            ->andX(
                                $queryBuilder->expr()->inSet( 'usergroup',$groupUid),
                                $queryBuilder->expr()->eq('disable',0)
                            )
                    )
                ];
            }
        }
        $members->setMembers($users);
        $members->setNumberOfMembers($this->numberOfUsers);
        return $members;
    }

    /**
     * @param $groupUid
     * @return false|string[]
     */
    public function getSubGroupsUid($groupUid){
        $queryBuilder =  $this->generateQueryBuilder('be_groups');
        $result =   $queryBuilder
            ->select('subgroup')
            ->from('be_groups')
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($groupUid)),
            )
            ->execute()
            ->fetchOne();
        return explode(',', $result);
    }

    /**
     * @param $whereCondition
     * @return array
     */
    public function renderUsersData($whereCondition) : array {
        $usersUid = [];
        $data =  BackendUtility::getUserNames('uid, username,realName,email,lastlogin', $whereCondition);
        $users = [];
        foreach ($data as $item){
            // returns the number of all members - prevent to calculate the same member record multiple time
            if(!in_array($data['uid'], $usersUid)){
                $this->numberOfUsers++;
            }
            // create Member Object
            $users[] = $this->memberMap($item);
        }
        return $users;
    }

    /**
     * @param array $data
     * @return Member
     */
    public function memberMap(array $data) : Member{
        $member = new Member();
        $member->setUid($data['uid']);
        $member->setUsername($data['username']);
        $member->setRealName($data['realName']);
        $member->setEmail($data['email']);
        if($data['lastlogin'] === 0){
            $member->setLastLogin(
                 $this->localizationUtility->translate(Self::LANG_FILE . 'userHasNeverLoggedIn')
            );
        }
        else{
            $member->setLastLogin(date("Y-m-d H:i:s", $data['lastlogin']));
        }
        return $member;
    }
}