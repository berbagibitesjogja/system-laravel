<?php

namespace App\Console\Commands;

use App\Models\Volunteer\Cancelation;
use Illuminate\Console\Command;

class ClearCancelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:cancelation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Cancelation::whereDate('banned', now()->toDateString())->delete();
        Cancelation::whereNull('banned')->whereTries(1)->delete();
    }
}
