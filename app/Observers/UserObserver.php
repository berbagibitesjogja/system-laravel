<?php

namespace App\Observers;

use App\Models\Volunteer\Availability;
use App\Models\Volunteer\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        $id = $user->id;
        $data = [];
        for ($day = 1; $day <= 7; $day++) {
            for ($hour = 7; $hour <= 21; $hour++) {
                $data[] = [
                    "user_id" => $id,
                    "day" => $day,
                    "hour" => $hour,
                    "minute" => 0,
                    "code" => $day . $hour . "0"
                ];
                $data[] = [
                    "user_id" => $id,
                    "day" => $day,
                    "hour" => $hour,
                    "minute" => 30,
                    "code" => $day . $hour . "5"
                ];
            }
        }
        Availability::insert($data);
        $user->availabilities()->update(["created_at" => now(), "updated_at" => now()]);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
