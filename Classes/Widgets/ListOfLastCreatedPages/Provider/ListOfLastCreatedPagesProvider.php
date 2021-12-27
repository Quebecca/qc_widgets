<?php
namespace Qc\QcWidgets\Widgets\ListOfLastCreatedPages\Provider;

interface ListOfLastCreatedPagesProvider
{
    public function getTable() : string;
    public function getItems(): array;

}