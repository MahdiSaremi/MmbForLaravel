<?php

namespace Mmb\Laravel\Support\Db;

use Illuminate\Database\Eloquent\Model;

class FinderFactory
{

    protected array $caches = [];
    protected array $currents = [];

    /**
     * Find from model by id
     *
     * @param string $model
     * @param        $id
     * @param        $default
     * @return Model|mixed
     */
    public function find(string $model, $id, $default = null)
    {
        if(isset($this->caches[$model][$id]))
        {
            return $this->caches[$model][$id];
        }

        $object = $model::find($id);

        if($object === null)
        {
            return value($default);
        }

        return @$this->caches[$model][$id] = $object;
    }

    /**
     * Find by key and caching
     *
     * @param string $model
     * @param string $key
     * @param        $value
     * @param        $default
     * @return Model|mixed
     */
    public function findBy(string $model, string $key, $value, $default = null)
    {
        if($key === '')
        {
            return $this->find($model, $value, $default);
        }

        if(isset($this->caches[$model]))
        {
            /** @var Model $object */
            foreach($this->caches[$model] as $object)
            {
                if($object->getAttribute($key) == $value)
                {
                    return $object;
                }
            }
        }

        $object = $model::where($key, $value)->first();

        if($object === null)
        {
            return value($default);
        }

        return @$this->caches[$model][$object->getKey()] = $object;
    }

    /**
     * Find from model or fail
     *
     * @param string $model
     * @param        $id
     * @param int    $code
     * @return Model|mixed
     */
    public function findOrFail(string $model, $id, int $code = 404)
    {
        return $this->find($model, $id, fn() => abort($code));
    }

    /**
     * Store model to caches
     *
     * @param Model|array $model
     * @return Model|array
     */
    public function store(Model|array $model)
    {
        if(is_array($model))
        {
            foreach($model as $model2)
            {
                $this->store($model2);
            }

            return $model;
        }

        @$this->caches[$model::class][$model->getKey()] = $model;
        return $model;
    }

    /**
     * Forget model or model class from caches
     *
     * @param string|Model $model
     * @return void
     */
    public function forget(string|Model $model)
    {
        if(is_string($model))
        {
            unset($this->caches[$model]);
        }
        else
        {
            unset($this->caches[$model::class][$model->getKey()]);
        }
    }

    /**
     * Clear caches
     *
     * @return void
     */
    public function clear()
    {
        $this->caches = [];
    }

    /**
     * Store current of model to cache
     *
     * @param Model $model
     * @return Model
     */
    public function storeCurrent(Model $model)
    {
        $this->store($model);
        $this->currents[$model::class] = $model;

        return $model;
    }

    /**
     * Get current model
     *
     * @template T
     * @param class-string<T> $model
     * @return ?T
     */
    public function current(string $model)
    {
        return @$this->currents[$model];
    }

}
