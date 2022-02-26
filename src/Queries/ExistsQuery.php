<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

class ExistsQuery implements Query
{
    protected $field;

    public static function create(
        string $field
    ): self
    {
        return new self($field);
    }

    public function __construct($field)
    {
        $this->field = $field;
    }

    public function toArray(): array
    {
        return [
            'exists' => [
                'field' => $this->field,
            ],
        ];
    }
}
