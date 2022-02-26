<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

use Savingfor\ElasticsearchQueryBuilder\Exceptions\BoolQueryTypeDoesNotExist;

class BoolQuery implements Query
{
    protected $must = [];
    protected $filter = [];
    protected $should = [];
    protected $must_not = [];

    public static function create(): self
    {
        return new self();
    }

    public function add(Query $query, string $type = 'must'): self
    {
        if (!in_array($type, ['must', 'filter', 'should', 'must_not'])) {
            throw new BoolQueryTypeDoesNotExist($type);
        }

        $this->$type[] = $query;

        return $this;
    }

    public function toArray(): array
    {
        $bool = [
            'must' => array_map(function (Query $query) {
                return $query->toArray();
            }, $this->must),
            'filter' => array_map(function (Query $query) {
                return $query->toArray();
            }, $this->filter),
            'should' => array_map(function (Query $query) {
                return $query->toArray();
            }, $this->should),
            'must_not' => array_map(function (Query $query) {
                return $query->toArray();
            }, $this->must_not),
        ];

        return [
            'bool' => array_filter($bool),
        ];
    }
}
