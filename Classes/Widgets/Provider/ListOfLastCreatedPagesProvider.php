<?php
namespace Qc\QcWidgets\Widgets\Provider;

interface ListOfLastCreatedPagesProvider
{
    public function getTable() : string;
    public function getItems(): array;

}