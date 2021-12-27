<?php

namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities;

class ListOfMemebers
{
    /**
     * @var boolean
     */
    protected bool $isAdmin;

    /**
     * @var array
     * this array has to structure, the group name, and the array of members
     */
    protected array $members = [];

    /**
     * @var int
     */
    protected int $numberOfMembers = 0;

    /**
     * @return int
     */
    public function getNumberOfMembers(): int
    {
        return $this->numberOfMembers;
    }

    /**
     * @param int $numberOfMembers
     */
    public function setNumberOfMembers(int $numberOfMembers): void
    {
        $this->numberOfMembers = $numberOfMembers;
    }



    /**
     * @return bool
     */
    public function getIsAdmin(): bool
    {
        return $this->isAdmin;
    }


    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return array
     */
    public function getMembers(): array
    {
        return $this->members;
    }

    /**
     * @param array $members
     */
    public function setMembers(array $members): void
    {
        $this->members = $members;
    }

}