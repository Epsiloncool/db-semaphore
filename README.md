# db-semaphore

A short and effective semaphore implementation in pure PHP

This library could be used if you want to manage one piece of the PHP code which have to be run only once.

The great place where it could be used is a cronjob script. While cron job will be runned periodically by the OS, 
we should not run the script if it's still executing from the previous run. Semaphore will help in this situation.

## Installation

Install the latest version with

```
$ composer require epsiloncool/db-semaphore
```

Import sql dump from the file `sql/dbsem_locks.sql`

## Basic Usage
```
<?php

use Epsiloncool\Utils;

$db = GetDBLink();	// Get MySQLi instance from your app environment

$process_id = md5(uniqid('random_process_id'));	// Create random process ID

global $sem;

// Create a new semaphore for this task "task_identifier"
$sem = new DB_Semaphore($db, 'dbsem', $process_id, 'task_identifier');
$sem->timeout = 600;	// Set a time, when the semaphore will be busy with this process even on process fail

// Trying to reserve a semaphore for this process
if (!$sem->Enter()) {
	echo 'Another instance of this process is running. Stopped.'."\n";
	exit();
}

// Ok, we allowed to execute

// ... Do some actions
$sem->Update();	// We have to call this method periodically with the guaranteed interval of half of "timeout" value

// ...More actions
$sem->Update(); // Remember to call this periodically

// Release the semaphore on the end of the task (allow to rerun this task)
$sem->Leave();

echo 'This task is completed';


```

## Change log

### v1.0.2

- detailed documentation

### v1.0.1

- added Check() method

### v1.0.0

- initial release