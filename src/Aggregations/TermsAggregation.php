<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations;

use Savingfor\ElasticsearchQueryBuilder\AggregationCollection;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns\WithAggregations;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns\WithMissing;

class TermsAggregation extends Aggregation
{
    use WithMissing;
    use WithAggregations;

    protected $field;

    protected $size = null;

    protected $order = null;

    public static function create(string $name, string $field): self
    {
        return new self($name, $field);
    }

    public function __construct(string $name, string $field)
    {
        $this->name = $name;
        $this->field = $field;
        $this->aggregations = new AggregationCollection();
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function order(array $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function payload(): array
    {
        $parameters = [
            'field' => $this->field,
        ];

        if ($this->size) {
            $parameters['size'] = $this->size;
        }

        if ($this->missing) {
            $parameters['missing'] = $this->missing;
        }

        if ($this->order) {
            $parameters['order'] = $this->order;
        }

        $aggregation = [
            'terms' => $parameters,
        ];

        if (!$this->aggregations->isEmpty()) {
            $aggregation['aggs'] = $this->aggregations->toArray();
        }

        return $aggregation;
    }
}
