<?php
namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp;

use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities\GroupOfMember;
use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities\ListOfMemebers;
use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities\Member;
use Qc\QcWidgets\Widgets\ListOfMembers\Provider\ListOfMembersProvider;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Beuser\Domain\Repository\BackendUserRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;

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

    }

    public function getTable(): string
    {
        return $this->table;
    }
    // $this->setWorkspace($this->user['workspace_id']);

    public function getItems(): ListOfMemebers
    {
        $users = [];
        $members = new ListOfMemebers();
        $members->setIsAdmin($GLOBALS['BE_USER']->isAdmin());
        if($GLOBALS['BE_USER']->isAdmin()){
            $users [] = [
                'groupName' => '',
                'users' => $this->renderUsersData("AND ADMIN = 1 AND disable = 0")
            ];
        }
        else{
            $groupsUid = explode(',', $GLOBALS['BE_USER']->user['usergroup']);
            foreach ($groupsUid as $groupUid){
                 $groupName = $this->backendUserGroupRepository->findByUid($groupUid);
                $users [] = [
                    'groupName' => $groupName->getTitle(),
                    'users' => $this->renderUsersData("AND usergroup LIKE  '%$groupUid%'  AND disable = 0")
                ];
            }
        }
        $members->setMembers($users);
        return $members;
    }

    public function renderUsersData($whereCondition) : array{
        $data =  BackendUtility::getUserNames('username,realName,email,lastlogin', $whereCondition);
        $users = [];
        foreach ($data as $item){
            // create Member Object
            $users[] = $this->memberMappe($item);
        }
        return $users;
    }

    public function memberMappe(array $data) : Member{
        $member = new Member();
        $member->setUsername($data['username']);
        $member->setRealName($data['realName']);
        $member->setEmail($data['email']);
        $member->setLastLogin(date("Y-m-d H:i:s", $data['lastlogin']));

        // check if the email or realName is an empty value
        if ($member->getEmail() === '') {
            $member->setEmail(
                $this->localizationUtility->translate(Self::LANG_FILE . 'emailNotProvided')
            );
        }
        if ($member->getRealName() === '') {
            $member->setRealName(
                $this->localizationUtility->translate(Self::LANG_FILE . 'realNameNotProvided')
            );
        }
        return $member;
    }


    /**
     * @return BackendUserAuthentication
     */
    protected function getBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }
}