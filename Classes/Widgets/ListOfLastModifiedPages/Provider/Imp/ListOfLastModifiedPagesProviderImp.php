<?php

namespace Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\Imp;


use Qc\QcWidgets\Widgets\ListOfLastModifiedPages\Provider\ListOfLastModifiedPagesProvider;

class ListOfLastModifiedPagesProviderImp implements ListOfLastModifiedPagesProvider
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

    public function __construct(string $table, string $orderField, string $limit)
    {
        $this->table = $table;
        $this->orderField = $orderField;
        $this->limit = $limit;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getItems(): array
    {
        // render data from database
        return ['data1', 'data2', 'data3'];
    }
}