<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations;

use Savingfor\ElasticsearchQueryBuilder\Aggregations\Aggregation;
use Savingfor\ElasticsearchQueryBuilder\AggregationCollection;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns\WithAggregations;
use Savingfor\ElasticsearchQueryBuilder\Queries\Query;

class FilterAggregation extends Aggregation
{
    use WithAggregations;

    protected  $filter;

    public static function create(
        string $name,
        Query $filter,
        Aggregation ...$aggregations
    ): self {
        return new self($name, $filter, ...$aggregations);
    }

    public function __construct(
        string $name,
        Query $filter,
        Aggregation ...$aggregations
    ) {
        $this->name = $name;
        $this->filter = $filter;
        $this->aggregations = new AggregationCollection(...$aggregations);
    }

    public function payload(): array
    {
        return [
            'filter' => $this->filter->toArray(),
            'aggs' => $this->aggregations->toArray(),
        ];
    }
}
