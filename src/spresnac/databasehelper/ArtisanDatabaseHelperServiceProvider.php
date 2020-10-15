<?php

namespace spresnac\databasehelper;

use Illuminate\Support\ServiceProvider;

class ArtisanDatabaseHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BackupDatabase::class,
                RestoreDatabase::class,
                DropTables::class,
            ]);
        }
    }
}
