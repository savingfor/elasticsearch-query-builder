<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations;

abstract class Aggregation
{
    protected $name;

    protected $meta = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function meta(array $meta): self
    {
        $this->meta = array_merge($this->meta, $meta);

        return $this;
    }

    abstract public function payload(): array;

    public function toArray(): array
    {
        $payload = $this->payload();

        if (count($this->meta) > 0) {
            $payload['meta'] = $this->meta;
        }

        return $payload;
    }
}
