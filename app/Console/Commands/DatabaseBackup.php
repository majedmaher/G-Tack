<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Database Backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $config = config('database.connections.mysql');
        $file = $this->argument('file') ?? Carbon::now()->format('Y-m-d-H-i-s') . '.sql';
        $filename = storage_path('app/backups/' . $file);

        $command = "mysqldump -u {$config['username']} {$config['database']} > {$filename}";
        exec($command);

        $this->line('Backup completed: ' . $filename);

        return $filename;
    }
}
