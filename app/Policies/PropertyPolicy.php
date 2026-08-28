<?php

namespace App\Policies;

use App\Models\Property;
use App\Models\User;
use App\Policies\Concerns\ChecksPermission;

class PropertyPolicy
{
    use ChecksPermission;

    public function viewAny(User $user): bool
    {
        return $this->allow($user, 'properties.view');
    }

    public function view(User $user, Property $property): bool
    {
        return $this->allow($user, 'properties.view');
    }

    public function create(User $user): bool
    {
        return $this->allow($user, 'properties.create');
    }

    public function update(User $user, Property $property): bool
    {
        return $this->allow($user, 'properties.update');
    }

    public function delete(User $user, Property $property): bool
    {
        return $this->allow($user, 'properties.delete');
    }

    public function changeStatus(User $user, Property $property): bool
    {
        return $this->allow($user, 'properties.change_status');
    }
}
