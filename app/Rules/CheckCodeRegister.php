<?php

namespace App\Rules;

use App\Models\Activation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;


class CheckCodeRegister implements Rule
{
    /**
     * var
     */

    public $userId;
    /**
     * Create a new rule instance.
     *
     * @param $userId
     * @param $flag
     */

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $data = Activation::join('users' , 'activations.user_id' , 'users.id')
            ->where('activations.user_id', $this->userId)
            ->where('activations.code',$value)
            ->where('activations.expired_time' ,'>=', Carbon::now()->format('Y-m-d H:i:s'))
            ->where('activations.completed' , Activation::COMPLETED_FALSE)
            ->where('users.status', User::INACTIVE)
            ->first();
        return !empty($data);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('messages.request.input_exists', ['attribute' => "Mã code"]);
    }
}
