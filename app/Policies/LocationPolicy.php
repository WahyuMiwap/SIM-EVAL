<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator', 'magang'], true);
    }

    public function create(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator'], true);
    }

    public function update(User $u, Location $l): bool
    {
        return in_array($u->role, ['superadmin', 'operator'], true);
    }

    public function delete(User $u, Location $l): bool
    {
        if ($u->role === 'superadmin') {
            return true;
        }

        return $u->role === 'operator' && $l->events()->count() === 0;
    }
}
