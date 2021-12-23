<?php
namespace Qc\QcWidgets\Widgets\Provider;

interface ListOfMembersProvider
{
    public function getTable() : string;
    public function getItems(): array;

}