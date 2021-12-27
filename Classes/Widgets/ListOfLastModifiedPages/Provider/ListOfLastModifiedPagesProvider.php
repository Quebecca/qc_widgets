<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider;

interface ListOfLastModifiedPagesProvider
{
    public function getTable() : string;
    public function getItems() : array;
}