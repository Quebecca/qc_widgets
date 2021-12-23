<?php

namespace Qc\QcWidgets\Widgets\Provider;

interface ListOfLastModifiedPagesProvider
{
    public function getTable() : string;
    public function getItems() : array;
}