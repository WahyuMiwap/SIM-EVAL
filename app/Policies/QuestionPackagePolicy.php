<?php

namespace App\Policies;

use App\Models\QuestionPackage;
use App\Models\User;

class QuestionPackagePolicy
{
    public function viewAny(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator', 'magang'], true);
    }

    public function create(User $u): bool
    {
        return in_array($u->role, ['superadmin', 'operator'], true);
    }

    public function update(User $u, QuestionPackage $p): bool
    {
        return in_array($u->role, ['superadmin', 'operator'], true);
    }

    public function delete(User $u, QuestionPackage $p): bool
    {
        if ($u->role === 'superadmin') {
            return true;
        }
        if ($u->role !== 'operator') {
            return false;
        }

        return $p->eventsPre()->count() === 0 && $p->eventsPost()->count() === 0;
    }
}
