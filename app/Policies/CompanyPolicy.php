<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool { return $user->can('view companies'); }
    public function view(User $user, Company $company): bool { return $user->can('view companies'); }
    public function create(User $user): bool { return $user->can('create companies'); }
    public function update(User $user, Company $company): bool { return $user->can('update companies'); }
    public function delete(User $user, Company $company): bool { return $user->can('delete companies'); }
}
