<?php

namespace Mmb\Laravel\Action\Update;

use Mmb\Laravel\Action\Action;
use Mmb\Laravel\Action\Memory\Step;
use Mmb\Laravel\Action\Memory\StepHandlerPipe;
use Mmb\Laravel\Support\Db\ModelFinder;
use Mmb\Laravel\Support\Step\Stepping;

class UpdateHandler extends Action
{

    /**
     * Update handler condition
     *
     * @return bool
     */
    public function condition() : bool
    {
        return $this->ifModel();
    }

    /**
     * Handle group
     *
     * @return void
     */
    public function handle()
    {
        Step::setModel($this->stepping());

        if($model = $this->getModel())
        {
            ModelFinder::storeCurrent($model);
        }

        $this->update->isHandled = false;
        foreach($this->list() as $handler)
        {
            if($handler === null)
            {
                continue;
            }

            if(!is_a($handler, UpdateHandling::class, true))
            {
                throw new \TypeError("Expected [".UpdateHandling::class."], given [". (is_string($handler) ? $handler : get_class($handler)) ."]");
            }

            if(is_string($handler))
            {
                $handler = new $handler;
            }

            $this->update->isHandled = true;
            $handler->handleUpdate($this->update);

            if($this->update->isHandled)
            {
                break;
            }
        }

        $this->final();
    }

    /**
     * Get list of handling
     *
     * @return UpdateHandling[]
     */
    public function list() : array
    {
        return [
            $this->step(),
        ];
    }

    /**
     * Related model
     *
     * @return mixed
     */
    public function model()
    {
        return null;
    }

    /**
     * Get current step
     *
     * @return ?StepHandlerPipe
     */
    public function step()
    {
        if($stepping = $this->stepping())
        {
            return new StepHandlerPipe($stepping);
        }

        return null;
    }

    /**
     * Get stepping
     *
     * @return Stepping|null
     */
    public function stepping() : ?Stepping
    {
        return $this->getModel();
    }

    private $_model;

    /**
     * Get model
     *
     * @return mixed|null
     */
    public function getModel()
    {
        return $this->_model ??= $this->model();
    }

    /**
     * @return bool
     */
    public function ifModel()
    {
        return $this->getModel() !== null;
    }

    /**
     * Final work
     *
     * @return void
     */
    public function final()
    {
        $this->getModel()?->save();
    }

}
