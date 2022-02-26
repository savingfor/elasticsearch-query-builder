<?php

namespace Savingfor\ElasticsearchQueryBuilder\Sorts;

class Sort
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    protected  $field;

    protected  $order;

    protected  $missing = null;

    protected  $unmappedType = null;

    public static function create(string $field, string $order = 'desc'): self
    {
        return new self($field, $order);
    }

    public function __construct(string $field, string $order)
    {
        $this->field = $field;
        $this->order = $order;
    }

    public function missing(string $missing): self
    {
        $this->missing = $missing;

        return $this;
    }

    public function unmappedType(string $unmappedType): self
    {
        $this->unmappedType = $unmappedType;

        return $this;
    }

    public function toArray(): array
    {
        $payload = [
            'order' => $this->order,
        ];

        if ($this->missing) {
            $payload['missing'] = $this->missing;
        }

        if ($this->unmappedType) {
            $payload['unmapped_type'] = $this->unmappedType;
        }

        return [
            $this->field => $payload,
        ];
    }
}
