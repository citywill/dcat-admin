<?php

namespace Dcat\Admin\Grid\Filter;

use Dcat\Admin\Grid\Filter;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * @mixin Builder
 */
class Scope implements Renderable
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    public $key = '';

    /**
     * @var string
     */
    protected $label = '';

    /**
     * @var Collection
     */
    protected $queries;

    /**
     * Scope constructor.
     *
     * @param Filter $filter
     * @param string $key
     * @param string $label
     */
    public function __construct(Filter $filter, $key, $label = '')
    {
        $this->filter = $filter;
        $this->key = $key;
        $this->label = $label ?: admin_trans_field($key);

        $this->queries = new Collection();
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get model query conditions.
     *
     * @return array
     */
    public function condition()
    {
        return $this->queries->map(function ($query) {
            return [$query['method'] => $query['arguments']];
        })->toArray();
    }

    /**
     * @return string
     */
    public function render()
    {
        $url = request()->fullUrlWithQuery(
            [$this->filter->getScopeQueryName() => $this->key]
        );

        return "<li><a href=\"{$url}\">{$this->label}</a></li>";
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return $this
     */
    public function __call($method, $arguments)
    {
        $this->queries->push(compact('method', 'arguments'));

        return $this;
    }
}