# Laravel: Usefull Database helper
This package will provide you some helper for
* Backup your database
* Restore this backups
* Drop all tables from a schema

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge&logo=github)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/spresnac/laravel-artisan-database-helper.svg?style=for-the-badge&logo=php)](https://packagist.org/packages/spresnac/laravel-artisan-database-helper)
[![Laravel Version](https://img.shields.io/badge/Laravel-%5E7%20|%20%5E8-important?style=for-the-badge&logo=laravel)](https://laravel.com)

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
![Downloads](https://img.shields.io/packagist/dt/spresnac/laravel-artisan-database-helper.svg)

# Installation
First things first, so require the package:

```
composer require spresnac/laravel-artisan-database-helper
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

#### Export with a date prefix
You can export with the actual date as a prefix to the export file name. It will look like `20190425_yourConnection.sql`.
```
php artisan db:backup -D
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
When used with `-D` it will look like
```
storage/app/backups/<Ymd_><connection>_backup.sql
```

## DropTable
With this command you can quickly "emtpy" a given database schema without deleting the schema itself. For short, all tables in the given connection will be deleted.
```
php artisan db:drop-tables <connection>
```

If you want to use this in an automatic way, you can use the `--force` option to delete the tables without confirmation (you be warned!).
```
php artisan db:drop-tables <connection> --force
```

## RestoreDatabase
To restore a backup, simply use
```
php artisan db:restore
```
You can provide more options in case you need one of this:

```
php artisan db:restore <backup_name> <connection> <path_to_mysql> <port>
```

All this options are similar to the ones described in `db:backup`, so you are able to seamlessly restore a backuped database.

# How i use it for testing or bugfixing my apps
When having complex setups for very complex bugs it may happen that you are in need of some very specific database entries you do not want to reproduce all the time.

In this case, i do use this package as a helper for me to bugfix faster.
1. Create a backup from the database with `db:backup`.
2. Rename this backup like the bugticket i have.
3. Setup my unit-test for this bug in a special group, that is not executed by default.
4. In my testscript, i do define a testschema.
5. Before running my tests, execute `db:drop-tables` on the testschema.
6. Right after that, execute `db:restore <ticketnumber>`.

With this, everytime i execute my tests for bugfing my database is reset to this very specific point where i can reproduce the bug and fix it fast.

# Finally
... have fun 😉 and be productive with it.
