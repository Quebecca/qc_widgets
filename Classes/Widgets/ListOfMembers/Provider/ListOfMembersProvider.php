<?php
namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider;

use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities\ListOfMemebers;

interface ListOfMembersProvider
{
    public function getTable() : string;
    public function getItems(): ListOfMemebers;

}