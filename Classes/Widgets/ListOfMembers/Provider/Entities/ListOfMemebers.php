<?php

/***
 *
 * This file is part of Qc Widgets project.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 <techno@quebec.ca>
 *
 ***/

namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider\Entities;

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

    public function setMembers(array $members): void
    {
        $this->members = $members;
    }

}