<?php

namespace App\Rules;

use App\Project;
use Illuminate\Contracts\Validation\Rule;

class ProjectNameRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    protected $userId;

    public function __construct(int $userId)
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

        if ($this->userId == 0){
            return true;
        }

        return $value == Project::where('user_id', $this->userId)->id;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
