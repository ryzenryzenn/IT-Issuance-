<?php

namespace App\Policies;

use App\Models\Asset;
use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view assets');
    }

    public function view(User $user, Asset $asset): bool
    {
        return $user->can('view assets');
    }

    public function create(User $user): bool
    {
        return $user->can('create assets');
    }

    public function update(User $user, Asset $asset): bool
    {
        return $user->can('update assets');
    }

    public function delete(User $user, Asset $asset): bool
    {
        return $user->can('delete assets');
    }

    public function transfer(User $user, Asset $asset): bool
    {
        return $user->can('transfer assets');
    }

    public function uploadAccountability(User $user, Asset $asset): bool
    {
        return $user->can('upload accountability files');
    }
}
