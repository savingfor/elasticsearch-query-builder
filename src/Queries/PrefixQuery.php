<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

class PrefixQuery implements Query
{
    protected $field;
    protected $query;

    public static function create(
        string $field,
        $query
    ): self
    {
        return new self($field, $query);
    }

    public function __construct(
        string $field,
        $query
    )
    {
        $this->query = $query;
        $this->field = $field;
    }

    public function toArray(): array
    {
        return [
            'prefix' => [
                $this->field => [
                    'value' => $this->query,
                ],
            ],
        ];
    }
}
