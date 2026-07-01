<?php

namespace App\Policies;

use App\Models\AssetModel;
use App\Models\User;

class AssetModelPolicy
{
    public function viewAny(User $user): bool { return $user->can('view asset models'); }
    public function view(User $user, AssetModel $assetModel): bool { return $user->can('view asset models'); }
    public function create(User $user): bool { return $user->can('create asset models'); }
    public function update(User $user, AssetModel $assetModel): bool { return $user->can('update asset models'); }
    public function delete(User $user, AssetModel $assetModel): bool { return $user->can('delete asset models'); }
}
