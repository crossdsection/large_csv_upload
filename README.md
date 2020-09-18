This is a project for submission for an Machine Round, I never used Laravel before this -

## Install 

 - `git clone https://github.com/crossdsection/large_csv_upload`
 - `composer install`
 - set MySql credentials in database.php
 - `php artisan migrate`

## Usage 
 - `php artisan serve`
 - `php artisan queue:listen --timeout=0` //Run in seperate terminal
 - <domain>/uploadfile -> Submit Zip URL of File containing CSV // http://spotrix.itarmsg.com/app/task/dev.zip
 	- This will trigger the downloading and extraction which is visible in the queue logs.
 	- status property corresponding to the file in table `files` will convert to SUCCESS 

## Specific View For the Machine Round  	
 - After the importing of CSV is completed
 	- Change MySql Configuration in my.ini and restart
 		#[mysqld] //Config
		`innodb_locks_unsafe_for_binlog=1`

	- Run SQL query mentioned in file `./queries/viewCreated.sql`
 - Open <domain>/uploadfile