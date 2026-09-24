<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function viewAny(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator', 'magang'], true);
    }

    public function view(User $u, Event $e): bool
    {
        return $this->viewAny($u);
    }

    public function create(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator'], true);
    }

    public function update(User $u, Event $e): bool
    {
        if ($u->role === 'superadmin') {
            return true;
        }
        if ($u->role !== 'operator') {
            return false;
        }

        return strtolower($e->status ?? '') === 'dijadwalkan' && $e->participants()->count() === 0;
    }

    public function delete(User $u, Event $e): bool
    {
        if ($u->role === 'superadmin') {
            return true;
        }

        return $u->role === 'operator' && $this->update($u, $e);
    }
}
