<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class mp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mp:inspector';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check MoodProvider status...';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Checking for resources...');
    }
}
