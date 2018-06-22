<?php namespace Nano7\Tenants;

use Nano7\Database\Model\Model;
use Nano7\Database\Model\Scope;
use Nano7\Database\Query\Builder as QueryBuilder;

class UserScope extends Scope
{
    /**
     * Id of scope.
     * @var string
     */
    protected $name = 'user';

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  QueryBuilder  $builder
     * @param  Model  $model
     * @return void
     */
    public function apply(QueryBuilder $builder, Model $model)
    {
        $builder->where('user_id', auth()->id());
    }
}