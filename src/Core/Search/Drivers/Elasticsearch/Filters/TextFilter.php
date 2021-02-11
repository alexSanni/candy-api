<?php

namespace GetCandy\Api\Core\Search\Drivers\Elasticsearch\Filters;

use Elastica\Query\BoolQuery;
use Elastica\Query\Match;
use Elastica\Query\Range;
use GetCandy\Api\Core\Search\Providers\Elastic\Aggregators\Attribute;

class TextFilter extends AbstractFilter
{
    public $handle = 'text-filter';
    protected $field;
    protected $value;

    public function getQuery()
    {
        $filter = new BoolQuery;
        foreach ($this->value as $value) {
            if (strpos($value, '|')) {
                $value = explode('|', $value);
            }
            if (is_array($value)) {
                // If the first value is not numeric, then we assume we are
                // matching across multiple text values for a field, not a range.
                if (!is_numeric($value[0])) {
                    foreach  ($value as $val) {
                        $match = new Match;
                        $match->setFieldAnalyzer($this->field.'.filter', 'keyword');
                        $match->setFieldQuery($this->field.'.filter', $val);
                        $filter->addShould($match);
                    }
                } else {
                    $range = new Range($this->field.'.filter', [
                        'gte' => (int) $value[0],
                        'lte' => $value[1] == '*' ? null : (int) $value[1],
                    ]);
                    $filter->addShould($range);
                }
            } else {
                $match = new Match;
                $match->setFieldAnalyzer($this->field.'.filter', 'keyword');
                $match->setFieldQuery($this->field.'.filter', $value);

                $filter->addShould($match);
            }
        }

        return $filter;
    }

    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get an aggregation based on this filter.
     *
     * @return \GetCandy\Api\Core\Search\Providers\Elastic\Aggregators\Attribute
     */
    public function aggregate()
    {
        return new Attribute($this->field);
    }

    public function process($payload, $type = null)
    {
        $this->field = $type;
        $this->value = explode(':', $payload);

        return $this;
    }
}
