<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

class NestedQuery implements Query
{
    protected $path;

    protected $query;

    public static function create(string $path, Query $query): self
    {
        return new self($path, $query);
    }

    public function __construct(
        string $path,
        Query $query
    )
    {
        $this->path = $path;
        $this->query = $query;
    }

    public function toArray(): array
    {
        return [
            'nested' => [
                'path' => $this->path,
                'query' => $this->query->toArray(),
            ],
        ];
    }
}
