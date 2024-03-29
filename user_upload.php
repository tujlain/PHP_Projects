<?php

    define('DB_DETAILS_FILE', getcwd() . '\db_details.txt');
    $command_options_to_prompt = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    // Array of database options to prompt for
    $db_command_options_to_prompt = [
        'u' => 'MySQL username',
        'p' => 'MySQL password',
        'h' => 'MySQL host',
    ];

    if (isset($command_options_to_prompt['help'])) {
        echo "Usage: php user_upload.php [--file=filename] [--create_table] [--dry_run] [-u username] [-p password] [-h host] [--help]\n";
        echo "Options:\n";
        echo "  --file=filename   Specify the CSV file to be parsed\n";
        echo "  --create_table    Build the MySQL users table and exit\n";
        echo "  --dry_run         Run the script without altering the database\n";
        echo "  -u                MySQL username\n";
        echo "  -p                MySQL password\n";
        echo "  -h                MySQL host\n";
        echo "  --help            Display this help message\n";
        exit(0);
    }

    if (isset($command_options_to_prompt['file'])) {
        $file_path = getcwd(). '\\'. $command_options_to_prompt['file'];
        if (file_exists($file_path))
        {
            // Read the file
            try{
                $file_contents = file_get_contents($file_path);
                if (isset($command_options_to_prompt['dry_run'])) {
                    echo "Dry run mode: Database won't be altered.\n";
                    processFileData($file_contents);
                } else {
                    processFileData($file_contents);
                    // Insert data into the database (if not in dry run mode)
                    echo "Inserting data into the database...\n";
                    // Your database insertion code goes here...
                }
            }
            catch (Exception $e)
            {
                echo 'There was an error in reading the file: '.$e; 
            }
            
        }
        else{
            echo "This file does not exist in the current directory. Try Again!";
            exit;
        }

    }

    function processFileData($file_contents)
    {
        echo 'processing data';
    }

    // Function to prompt for missing db options
    function promptForOption($option, $prompt) {
        global $command_options_to_prompt;
        if (!getDbInfo($option))
        {
            echo "Enter $prompt: ";
            $command_options_to_prompt[$option] = trim(fgets(STDIN));
            writeDBInfo($option, $command_options_to_prompt);
        }
    }

    function writeDBInfo($option, $command_options_to_prompt)
    {
        $db_details = parse_ini_file(DB_DETAILS_FILE);

        // Update option value if it exists, otherwise append the option
        if (isset($command_options_to_prompt[$option])) {
            $db_details[$option] = $command_options_to_prompt[$option];
        } else {
            // Append the option to the database details array
            $db_details[$option] = '';
        }

        // Construct content to write
        $content = '';
        foreach ($db_details as $db_detail => $value) {
            $content .= "$db_detail=$value\n";
        }

        // Write content to file
        file_put_contents(DB_DETAILS_FILE, $content);
        
    }
    
    function getDbInfo($option)
    {
        $db_details = parse_ini_file(DB_DETAILS_FILE);

        // Update option value if it exists, otherwise append the option
        if (isset($db_details[$option]))
        {
            return 1;
        }
         else {
            return 0;
        }
    }


        // Function to establish the database connection
        function connectToDatabase($host, $username, $password, $database) {
            // Create connection
            $dbconnection = new mysqli($host, $username, $password);

            // Check connection
            if ($dbconnection->connect_error) {
                die("Connection failed: " . $dbconnection->connect_error);
            }

            // Create database if it doesn't exist
            $sql = "CREATE DATABASE IF NOT EXISTS $database";
            if ($dbconnection->query($sql) === TRUE) {
                echo "Database created successfully or already exists\n";
            } else {
                echo "Error creating database: " . $dbconnection->error;
            }

            // Close connection
            $dbconnection->close();

            // Reconnect with the specified database
            $conn = new mysqli($host, $username, $password, $database);

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            return $conn;
        }

    function connectToSql($db_details)
    {
        $username = $db_details['u'];
        $password = $db_details['p'];
        $host = $db_details['h'];
        $database = "tarudb";
        $sql = " CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                surname VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE
            )";

        try{
            $sqlconnection = connectToDatabase($host, $username, $password, $database);
            if ($sqlconnection instanceof mysqli) {
                echo "Connected successfully to database: $database\n";

                echo "Creating Table if it does not exist: ";

                // Execute the query to create/update table
                if ($sqlconnection->query($sql) === TRUE) {
                    echo "Table created.\n";
                } else {
                    echo "Error creating table: " . $sqlconnection->error;
                }
            } else {
                echo "Connection failed";
            }
        }

        catch (Exception $e)
        {
            echo $e;
        }
    }



    while (True){

    // Check if database options are set through command-line arguments
    foreach ($db_command_options_to_prompt as $option => $prompt) {
        if (isset($command_options_to_prompt[$option])) {
            // Set the option value directly from command-line argument
            // $command_options_to_prompt[$option] = trim(fgets(STDIN));
            // Save the entered option to a file
            echo $command_options_to_prompt[$option];
            // file_put_contents(DB_DETAILS_FILE, "$option={$command_options_to_prompt[$option]}\n", FILE_APPEND);
             
            try{
                writeDBInfo($option, $command_options_to_prompt);
                exit;
            }
            catch (Exception $e)
            {
                echo $e;
            }
            
        }
    }

    // Prompt for creating table
    if (isset($command_options_to_prompt['create_table'])) {
        foreach ($db_command_options_to_prompt as $option => $prompt) {
            promptForOption($option, $prompt);
        }
            $db_details = parse_ini_file(DB_DETAILS_FILE);
            connectToSql($db_details);
    }

    echo "Do you wish to continue? (yes/no): ";
    $response = trim(fgets(STDIN));
    if ($response !== 'yes') {
        break; // Exit the loop if the user doesn't want to continue
    }
    else{
        continue;
    }

} // While loop closing



?>