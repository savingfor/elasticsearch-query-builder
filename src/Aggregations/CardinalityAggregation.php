<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations;

use Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns\WithMissing;

class CardinalityAggregation extends Aggregation
{
    use WithMissing;

    protected  $field;

    protected $precisionThreshold = null;

    public static function create(string $name, string $field): self
    {
        return new self($name, $field);
    }

    public function __construct(string $name, string $field)
    {
        $this->name = $name;
        $this->field = $field;
    }

    public function precisionThreshold(int $precisionThreshold):self
    {
        $this->precisionThreshold = $precisionThreshold;

        return $this;
    }

    public function payload(): array
    {
        $parameters = [
            'field' => $this->field,
        ];

        if ($this->precisionThreshold) {
            $parameters["precision_threshold"] = $this->precisionThreshold;
        }

        if ($this->missing) {
            $parameters['missing'] = $this->missing;
        }

        return [
            'cardinality' => $parameters,
        ];
    }
}
