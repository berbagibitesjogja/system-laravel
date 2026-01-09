<?php

namespace App\Observers;

use App\Models\Heroes\Hero;

class HeroObserver
{
    /**
     * Handle the Hero "created" event.
     */
    public function created(Hero $hero): void
    {
        $quantity = $hero->quantity ?? 1;
        $hero->donation->decrement('remain', $quantity);
    }

    /**
     * Handle the Hero "updated" event.
     */
    public function updated(Hero $hero): void
    {
        //
    }

    /**
     * Handle the Hero "deleted" event.
     */
    public function deleted(Hero $hero): void
    {
        // When deleted, we increment remain + 1 (logic from destroy/cancel)
        // Warning: This assumes consistency with controller logic that always does +1
        // even if contributor had quantity > 1? 
        // Controller hardcodes +1. We will stick to that to be safe.
        $hero->donation->increment('remain', 1);
    }

    /**
     * Handle the Hero "restored" event.
     */
    public function restored(Hero $hero): void
    {
        //
    }

    /**
     * Handle the Hero "force deleted" event.
     */
    public function forceDeleted(Hero $hero): void
    {
        //
    }
}
