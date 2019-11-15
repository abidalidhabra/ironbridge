<?php

namespace App\Rules\Hunt;

use App\Models\v1\User;
use App\Repositories\Hunt\HuntUserDetailRepository;
use Illuminate\Contracts\Validation\Rule;

class UseTheSkeletonKeyRule implements Rule
{
    private $userId;
    private $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        
        $huntUser = (new HuntUserDetailRepository)->find($value)->hunt_user;

        if ($huntUser) {
            if ($huntUser->user_id != $this->userId) {
                $this->message = 'You are not authorized to do action in this clue.';
                return false;
            }else if ($huntUser->status == 'completed') {
                $this->message = 'You cannot use skeleton key in this clue, as it already ended.';
                return false;
            }else {
                $skeletonExists = User::where(['skeleton_keys.used_at' => null, '_id'=> $this->userId])->count();
                if (!$skeletonExists) {
                    $this->message = 'You do not have sufficient skeleton keys.';
                    return false;
                }
                return true;
            }
        }else{
            $this->message = 'Invalid hunt user detail id provided.';
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
