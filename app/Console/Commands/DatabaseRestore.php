<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore Database Backup';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = config('database.connections.mysql');
        $filename = storage_path('app/backups/' . $this->argument('file'));
        if (!file_exists($filename)) {
            $this->line('restore file not exists');
            return Command::FAILURE;
        }

        $command = "mysql -u {$config['username']} {$config['database']} < {$filename}";

        exec($command);
        
        $this->line('Backup restored: ' . $config['database']);

        return Command::SUCCESS;
    }
}
