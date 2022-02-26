<?php

namespace Savingfor\ElasticsearchQueryBuilder\Aggregations\Concerns;

trait WithMissing
{
    protected  $missing = null;

    public function missing(string $missingValue): self
    {
        $this->missing = $missingValue;

        return $this;
    }
}
