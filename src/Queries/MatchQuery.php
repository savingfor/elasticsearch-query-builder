<?php

namespace Savingfor\ElasticsearchQueryBuilder\Queries;

class MatchQuery implements Query
{
    protected $field;
    protected $query;
    protected $fuzziness = null;

    public static function create(
        string $field,
        $query,
        $fuzziness = null
    ): self
    {
        return new self($field, $query, $fuzziness);
    }

    public function __construct(
        $field, $query, $fuzziness = null
    )
    {
        $this->fuzziness = $fuzziness;
        $this->query = $query;
        $this->field = $field;
    }

    public function toArray(): array
    {
        $match = [
            'match' => [
                $this->field => [
                    'query' => $this->query,
                ],
            ],
        ];

        if ($this->fuzziness) {
            $match['match'][$this->field]['fuzziness'] = $this->fuzziness;
        }

        return $match;
    }
}
