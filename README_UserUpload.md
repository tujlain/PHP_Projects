# User Upload PHP Script

This PHP script is designed to parse a CSV file and insert its data after processing into a MySQL database.

## Requirements

1. PHP 7.0 or higher
2. MySQL database

## Installation

1. Clone this repository to your local machine.
2. Ensure you have PHP and a MySQL database set up.
3. Run the script via command line using the provided usage instructions.

## Usage

### Default Command
```php user_upload.php```

### Help Command
This command returns the details of all command line directives
```
php user_upload.php --help
```

### MySQL Connection Command
These commands set the username, password and host of the MySQL database.
```
php user_upload.php -u "username"
php user_upload.php -p "password"
php user_upload.php -h "host"
php user_upload.php --sqlport="portnumber"
```

### Create Table Command
The predefined database 'tarudb', will be created if it does not exist.

This command creates the 'users' table in the database if it does not exist.
```
php user_upload.php --create_table
```

### File Run Command
This command reads the specified CSV file, processes the file data, and updates the database based on the processed data. 

Rows with errors are skipped during processing.

Returns processed data with specified validations and formatting. For Eg: Capitalizing names, validating emails, trimming extra spaces etc.

Additionally, it returns the status of the inserted rows.
```
php user_upload.php --file=filename
```

### Dry Run Command
This command will read the file, process the file data and simulate running the script without altering the database.

Returns processed data with specified validations and formatting. For Eg: Capitalizing names, validating emails, trimming extra spaces etc.

It is always used in conjuction with the --file directive
```
php user_upload.php --file=filename --dry_run
```
