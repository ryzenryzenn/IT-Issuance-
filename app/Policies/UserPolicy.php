<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool { return $user->can('view users'); }
    public function view(User $user, User $target): bool { return $user->can('view users'); }
    public function create(User $user): bool { return $user->can('create users'); }

    public function update(User $user, User $target): bool
    {
        return $user->can('update users');
    }

    public function delete(User $user, User $target): bool
    {
        if ($user->id === $target->id) {
            return false;
        }
        return $user->can('delete users');
    }
}
