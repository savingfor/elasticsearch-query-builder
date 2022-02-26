<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

interface Query
{
    public function toArray(): array;
}
