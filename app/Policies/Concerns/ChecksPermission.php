<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait ChecksPermission
{
    protected function allow(User $user, string $permission): bool
    {
        return $user->can($permission);
    }
}
