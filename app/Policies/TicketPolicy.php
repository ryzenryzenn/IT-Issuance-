<?php

namespace App\Policies;

use App\Enums\Permission;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool { return $user->can(Permission::ViewTickets->value); }
    public function view(User $user, Ticket $ticket): bool { return $user->can(Permission::ViewTickets->value); }
    public function create(User $user): bool { return $user->can(Permission::CreateTickets->value); }
    public function update(User $user, Ticket $ticket): bool { return $user->can(Permission::UpdateTickets->value); }
    public function delete(User $user, Ticket $ticket): bool { return $user->can(Permission::DeleteTickets->value); }
}
