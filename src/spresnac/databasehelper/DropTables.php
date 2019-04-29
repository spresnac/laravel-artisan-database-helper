<?php

namespace spresnac\databasehelper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class DropTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop-tables
                            {connection=mysql : The connection to be used}
                            {--force : Do not ask questions, just do it ;)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drops all tables within your schema';

    protected $process;

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
     * @return mixed
     */
    public function handle()
    {
        if (Config::has('database.connections.' . $this->argument('connection')) === false) {
            $this->error('connection is unknown :(');
            exit();
        }
        if ($this->option('force') === true || $this->confirm('Drop all tables on connection ' . $this->argument('connection') . '?', true)) {
            Schema::connection($this->argument('connection'))->dropAllTables();
            $this->info('tables dropped :)');
            exit();
        }
        $this->info('dropping tables interupted...');
    }
}
