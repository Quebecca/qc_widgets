<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider;

use Qc\QcWidgets\Widgets\ListOfMembers\Provider\Imp\Entities\ListOfMemebers;

interface ListOfWorkspacePreviewLinksProvider
{
    public function getTable() : string;
    public function getItems(): array;
}