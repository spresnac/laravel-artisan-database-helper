<?php

namespace spresnac\databasehelper;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup 
                            {connection=mysql : The connection entry in your config, which schema to be exported} 
                            {path_to_mysql? : [Optional] Specify the path to you mysqldump binary}
                            {specified_port? : [Optional] Set port to connect on}
                            {--S|structure_only : Export only the structure of your schema}
                            {--O|skip_opt : Use --skip-opt on export}
                            {--D|date_prefix : Set a date prefix to the export file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database';

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
        if (Storage::exists('backups') === false) {
            Storage::makeDirectory('backups');
        }

        $name_suffix = 'backup';
        if (Config::has('database.connections.' . $this->argument('connection')) === false) {
            $this->error('connection is unknown :(');
            exit();
        }
        if ($this->hasArgument('path_to_mysql') === true && $this->argument('path_to_mysql') !== null) {
            chdir($this->argument('path_to_mysql'));
        }
        $command = 'mysqldump -u %1$s ';
        if (config('database.connections.' . $this->argument('connection') . '.password') !== '') {
            $command .= '-p%4$s ';
        }
        if ($this->hasArgument('specified_port') === true && $this->argument('specified_port') !== null) {
            $command .= '--port=' . $this->argument('specified_port') . ' ';
        }
        if ($this->option('structure_only')) {
            $command .= '-d ';
            $name_suffix = 'structure';
        }
        if ($this->option('skip_opt')) {
            $command .= '--skip-opt ';
        }
        $command .= '%2$s > %3$s';

        $export_file_name = $this->argument('connection') . '_'.$name_suffix.'.sql';
        if ($this->option('date_prefix')) {
            $export_file_name = date('Ymd') . '_' . $export_file_name;
        }

        try {
            $this->process = (new Process(sprintf(
                $command,
                config('database.connections.' . $this->argument('connection') . '.username'),
                config('database.connections.' . $this->argument('connection') . '.database'),
                storage_path('app' . DIRECTORY_SEPARATOR . 'backups' . DIRECTORY_SEPARATOR . $export_file_name),
                config('database.connections.' . $this->argument('connection') . '.password')
            )))->mustRun();
            $this->info('backups' . DIRECTORY_SEPARATOR . $this->argument('connection') . '_'.$name_suffix.'.sql created succesfull...');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
            $this->line($exception->getMessage());
            $this->line($exception->getTraceAsString());
        }
    }
}
