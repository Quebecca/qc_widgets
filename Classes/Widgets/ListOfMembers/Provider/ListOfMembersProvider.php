<?php
namespace Qc\QcWidgets\Widgets\ListOfMembers\Provider;

interface ListOfMembersProvider
{
    public function getTable() : string;
    public function getItems(): array;

}