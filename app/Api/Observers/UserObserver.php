<?php

namespace App\Api\Observers;

use App\Api\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        if ($user->isPassword($user->password) && $user->password) {
            $user->password = $user->passwordHash($user->password);
        }
    }

    public function updating(User $user)
    {
        if ($user->isPassword($user->password) && $user->isDirty('password')) {
            $user->password = $user->passwordHash($user->password);
        }
    }
}