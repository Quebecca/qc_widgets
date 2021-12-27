<?php

namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities;

class Member
{
    /**
     * @var int
     */
    protected int $uid = 0;

    /**
     * @var string
     */
    protected string $username;
    /**
     * @var string
     */
    protected string $email;
    /**
     * @var string
     */
    protected string $realName;
    /**
     * @var string
     */
    protected string $lastLogin;

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid): void
    {
        $this->uid = $uid;
    }



    /**
     * @return string
     */
    public function getLastLogin(): string
    {
        return $this->lastLogin;
    }

    /**
     * @param string $lastLogin
     */
    public function setLastLogin(string $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }


    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
       $this->email = $email;
    }

    /**
     * @return string
     */
    public function getRealName(): string
    {
        return $this->realName;
    }

    /**
     * @param string $realName
     */
    public function setRealName(string $realName): void
    {
        $this->realName = $realName;
    }

}