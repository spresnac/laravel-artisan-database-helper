# Laravel: Usefull Database helper
This package will provide you some helper for
* Backup your database
* Restore this backups
* Drop all tables from a schema
  
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![PHP from Packagist](https://img.shields.io/packagist/php-v/spresnac/laravel-artisan-database-helper.svg)
![Packagist](https://img.shields.io/packagist/l/spresnac/laravel-artisan-database-helper.svg)

# Installation
First things first, so require the package:

```
composer require spresnac/laravel-artisan-database-helper
```

Now, register the new command within your ``app\Console\Kernel.php``
```
    protected $commands = [
        // ...
        \spresnac\databasehelper\BackupDatabase::class,
        \spresnac\databasehelper\RestoreDatabase::class,
        \spresnac\databasehelper\DropTables::class,
    ];
```
That's all, you are ready to go now 😁

# Usage
## BackupDatabase
### The 90% way
To backup your database, open up your console and type
```
php artisan db:backup
```
and in most cases, you are done. You will find your backup in 
```
storage/app/backups
```
Ok, with "most cases", i meant this:
* your are using only one database
* you want to backup everthing
* you have set the path, so a call to `mysqldump` will work

If you have all of this, you are ready and good to go.

### The other 10% ;)
Let's say, you are not one of the 90%, perhaps because
* you use more than database connection
* you want to export only the structure
* you have a `mysqldump` but it is not in your path

I got you covered (like i had this problems too), so you can configure the way the backup is created with this options:

#### Define the connection to be backuped
With the first parameter, you can define the connection that is beeing used. You can set the connection within your ``config/database.php``
```
php artisan db:backup <connection_name>
```

#### Define the path to your mysqldump binary
Real world example needed for this option? Ok, short-format: I am using windows ... no more words needed ;)

The second parameter can be used to define a path that points to your ``mysqldump`` binary
```
php artisan db:backup <connection_name> <path_to_binary>
```
Hint: When you only need the path to be set, but use your default connection, use ``mysql`` as connection name:
```
php artisan db:backup mysql <path_to_binary>
```

#### Export only the structure
Use the ``-S`` option to export only the structure of your database:
```
php artisan db:backup -S
```

#### Export without options
This is one option that i personally need a lot. For details why, look in the 'How i use it for testing my apps' section.
```
php artisan db:backup -O
```

#### Glueing it all together
All glued together (export a specific database, with a custom path, structure only with no options in it):
```
php artisan db:backup foobardb d:/www/mysql/bin -SO
```

### Where is my backup?
You will find your backup in
```
storage/app/backups/<connection>_backup.sql
```
If you set the `-S` option, it looks like this
```
storage/app/backups/<connection>_structure.sql
```

## RestoreDatabase
Coming soon...
For this time, simply use
```
php artisan help db:drop-tables
```

## DropTable
Coming soon...
For this time, simply use
```
php artisan help db:restore
```

# How i use it for testing my apps
I have to write this down soon...

# Finally
... have fun ;)