<?php

namespace Signifly\Cancellation\Scopes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class CancellingScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = ['Keep', 'WithCancelled', 'WithoutCancelled', 'OnlyCancelled'];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        if (config('laravel-cancellation.exclude') === true) {
            $builder->whereNull($model->getQualifiedCancelledAtColumn());
        }
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Get the "cancelled at" column for the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return string
     */
    protected function getCancelledAtColumn(Builder $builder)
    {
        if (count($builder->getQuery()->joins) > 0) {
            return $builder->getModel()->getQualifiedCancelledAtColumn();
        }

        return $builder->getModel()->getCancelledAtColumn();
    }

    /**
     * Add the keep extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addKeep(Builder $builder)
    {
        $builder->macro('keep', function (Builder $builder) {
            $builder->withCancelled();

            return $builder->update([$builder->getModel()->getCancelledAtColumn() => null]);
        });
    }

    /**
     * Add the with-cancelled extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithCancelled(Builder $builder)
    {
        $builder->macro('withCancelled', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-cancelled extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addWithoutCancelled(Builder $builder)
    {
        $builder->macro('withoutCancelled', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNull(
                $model->getQualifiedCancelledAtColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-cancelled extension to the builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @return void
     */
    protected function addOnlyCancelled(Builder $builder)
    {
        $builder->macro('onlyCancelled', function (Builder $builder) {
            $model = $builder->getModel();

            $builder->withoutGlobalScope($this)->whereNotNull(
                $model->getQualifiedCancelledAtColumn()
            );

            return $builder;
        });
    }
}
