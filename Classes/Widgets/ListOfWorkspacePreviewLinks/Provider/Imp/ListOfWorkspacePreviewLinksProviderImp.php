<?php
namespace Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider\Imp;

use Qc\QcWidgets\Widgets\ListOfWorkspacePreviewLinks\Provider\ListOfWorkspacePreviewLinksProvider;

class ListOfWorkspacePreviewLinksProviderImp implements ListOfWorkspacePreviewLinksProvider
{
    /**
     * @var string
     */
    protected string $table = '';

    /**
     * @var string
     */
    protected string $orderField = '';

    /**
     * @var string
     */
    protected string $limit = '';

    /**
     * @var string
     */
    protected string $order= '';


    public function __construct(
        string $table,
        string $orderField,
        string $limit,
        string $order
    )
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
        $this->order = $order;
    }

    public function getTable(): string
    {
        return "";
    }

    public function getItems(): array
    {
        return [];
    }
}