<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->role === 'superadmin';
    }

    public function create(User $u): bool
    {
        return $u->role === 'superadmin';
    }

    public function update(User $u, User $target): bool
    {
        return $u->role === 'superadmin';
    }

    public function delete(User $u, User $target): bool
    {
        return $u->role === 'superadmin' && $u->id !== $target->id;
    }
}
