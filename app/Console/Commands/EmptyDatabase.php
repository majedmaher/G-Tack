<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EmptyDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:empty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Empty the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tables = [
            // 'users',
            // 'settings',
            'orders',
            'vendors',
            'customers',
        ];
        DB::statement("SET foreign_key_checks = 0");
        DB::statement('TRUNCATE ' . implode(', ', $tables));
        DB::statement("SET foreign_key_checks = 1");


        return Command::SUCCESS;
    }
}
