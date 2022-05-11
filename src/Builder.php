<?php

namespace Savingfor\ElasticsearchQueryBuilder;

use Elasticsearch\Client;
use Savingfor\ElasticsearchQueryBuilder\Aggregations\Aggregation;
use Savingfor\ElasticsearchQueryBuilder\Queries\BoolQuery;
use Savingfor\ElasticsearchQueryBuilder\Queries\Query;
use Savingfor\ElasticsearchQueryBuilder\Sorts\Sort;

class Builder
{
    protected $query = null;

    protected $aggregations = null;

    protected $sorts = null;

    protected $searchIndex = null;

    protected $size = null;

    protected $from = null;

    protected $searchAfter = null;

    protected $fields = null;

    protected $withAggregations = true;

    protected $trackTotalHits = false;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function addQuery(Query $query, string $boolType = 'must'): self
    {
        if (!$this->query) {
            $this->query = new BoolQuery();
        }

        $this->query->add($query, $boolType);

        return $this;
    }

    public function addAggregation(Aggregation $aggregation): self
    {
        if (!$this->aggregations) {
            $this->aggregations = new AggregationCollection();
        }

        $this->aggregations->add($aggregation);

        return $this;
    }

    public function addSort(Sort $sort): self
    {
        if (!$this->sorts) {
            $this->sorts = new SortCollection();
        }

        $this->sorts->add($sort);

        return $this;
    }

    public function search(): array
    {
        $payload = $this->getPayload();

        $params = [
            'body' => $payload,
        ];

        if ($this->searchIndex) {
            $params['index'] = $this->searchIndex;
        }

        if ($this->size !== null) {
            $params['size'] = $this->size;
        }

        if ($this->from !== null) {
            $params['from'] = $this->from;
        }

        if ($this->trackTotalHits) {
            $params["track_total_hits"] = $this->trackTotalHits;
        }

        return $this->client->search($params);
    }

    public function index(string $searchIndex): self
    {
        $this->searchIndex = $searchIndex;

        return $this;
    }

    public function size(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function from(int $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function searchAfter(?array $searchAfter): self
    {
        $this->searchAfter = $searchAfter;

        return $this;
    }

    public function fields(array $fields): self
    {
        $this->fields = array_merge($this->fields ?? [], $fields);

        return $this;
    }

    public function withoutAggregations(): self
    {
        $this->withAggregations = false;

        return $this;
    }

    public function getPayload(): array
    {
        $payload = [];

        if ($this->query) {
            $payload['query'] = $this->query->toArray();
        }

        if ($this->withAggregations && $this->aggregations) {
            $payload['aggs'] = $this->aggregations->toArray();
        }

        if ($this->sorts) {
            $payload['sort'] = $this->sorts->toArray();
        }

        if ($this->fields) {
            $payload['_source'] = $this->fields;
        }

        if ($this->searchAfter) {
            $payload['search_after'] = $this->searchAfter;
        }

        return $payload;
    }

    public function trackTotalHits( bool $trackTotalHits) : self {
        $this->trackTotalHits = $trackTotalHits;

        return $this;
    }
}
