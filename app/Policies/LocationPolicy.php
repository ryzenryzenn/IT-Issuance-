<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;

class LocationPolicy
{
    public function viewAny(User $user): bool { return $user->can('view locations'); }
    public function view(User $user, Location $location): bool { return $user->can('view locations'); }
    public function create(User $user): bool { return $user->can('create locations'); }
    public function update(User $user, Location $location): bool { return $user->can('update locations'); }
    public function delete(User $user, Location $location): bool { return $user->can('delete locations'); }
}
