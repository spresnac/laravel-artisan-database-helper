<?php

namespace spresnac\databasehelper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore
                            {backup=mysql_backup : The name of the backup file} 
                            {connection=mysql : The connection entry in your config, into which schema is imported}
                            {path_to_mysql? : [Optional] Specify the path to you mysql binary}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the database';

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
        if ($this->hasArgument('path_to_mysql') === true && $this->argument('path_to_mysql') !== null) {
            chdir($this->argument('path_to_mysql'));
        }
        $command = 'mysql -u %1$s ';
        if (config('database.connections.' . $this->argument('connection') . '.password') !== '') {
            $command .= '-p%4$s ';
        }
        $command .= '%2$s < "%3$s"';

        try {
            $this->process = (new Process(sprintf(
                $command,
                config('database.connections.' . $this->argument('connection') . '.username'),
                config('database.connections.' . $this->argument('connection') . '.database'),
                storage_path('app' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $this->argument('backup').'.sql'),
                config('database.connections.' . $this->argument('connection') . '.password')
            )))->mustRun();
            $this->info('database restored');
        } catch (ProcessFailedException $exception) {
            $this->error('The restore process has been failed.');
            $this->line($exception->getMessage());
            $this->line($exception->getTraceAsString());
        }
    }
}
