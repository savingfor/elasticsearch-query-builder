<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns;

use Savingfor\ElasticsearchQueryBuilder\AggregationCollection;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\Aggregation;

trait WithAggregations
{
    protected  $aggregations;

    public function aggregation(Aggregation $aggregation): self
    {
        $this->aggregations->add($aggregation);

        return $this;
    }
}
