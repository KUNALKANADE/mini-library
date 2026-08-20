<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id || $user->canManageCatalog();
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        // cancelling a reservation
        return $user->id === $reservation->user_id || $user->canManageCatalog();
    }
}
