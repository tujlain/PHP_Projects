# User Upload PHP Script

This PHP script is designed to parse a CSV file and insert its data after processing into a MySQL database.

## Requirements

1. PHP 8.0 or higher
2. MySQL database

## Assumptions
1. The PHP script assumes that a MySQL database server is already set up and accessible.
2. The user running the script has necessary permissions to create tables and insert data into the MySQL database.
3. For the `--dry_run` option, the script assumes that a valid filename is provided using the `--file` directive.
4. The CSV file provided for processing follows a specific format, with headers for each column (e.g., name, surname, email).
5. The DB configuration details will be saved into the db_details.txt file, useful for operations throughout the script.
6. MySQL not always is running on port 3306, hence the script has the command `--sqlport="portnumber"` to get the port number.
7. The DB configuration details can be requested for via two methods:

   a. Via individual command directives as given below under the Usage Guidelines [MySQL Connection Command]
   
   b. Via initiating the `--create_table` directive, the script would prompt the user to enter SQL username, password, host and port number. 
9. When using the `--create_table` option, the script would create the 'tarudb' database if it does not exist.

## Requirements
1. Clone this repository to your local machine.
2. Ensure you have PHP and a MySQL database set up.
3. Run the script via command line using the provided usage instructions.

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
These commands set the username, password, host and port number of the MySQL database.
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

![file command](https://github.com/tujlain/PHP_Projects/blob/main/assets/file_command.png)
```

### Dry Run Command
This command will read the file, process the file data and simulate running the script without altering the database.

Returns processed data with specified validations and formatting. For Eg: Capitalizing names, validating emails, trimming extra spaces etc.

It is always used in conjuction with the --file directive
```
php user_upload.php --file=filename --dry_run
```
