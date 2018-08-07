<?php namespace Nano7\Auth;

use Nano7\Database\Model\Model;

class UserSaving
{
    /**
     * Handle the event.
     *
     * @param  Model $model
     * @return void
     */
    public function saving(Model $model)
    {
        // Set user_id
        if (! $model->hasAttribute('user_id')) {
            $model->user_id = auth()->id();
        }
    }
}
