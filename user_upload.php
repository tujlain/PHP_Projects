<?php

    define('DB_DETAILS_FILE', getcwd() . '\db_details.txt');
    $command_options_to_prompt = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

    // Array of database options to prompt for
    $db_command_options_to_prompt = [
        'u' => 'MySQL username',
        'p' => 'MySQL password',
        'h' => 'MySQL host',
    ];

    function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    function capitalize($str) {
        return ucfirst(strtolower($str));
    }

    function processFileData($file_contents)
    {
        echo "Processing the File Data before insert\n";
        try{
            $row_data = array_map('str_getcsv', explode("\n", $file_contents));
            $csv_header = array_shift($row_data); // Removing the first row i.e, header
            $processed_data = array(); // Initialize an array to collect processed data

            foreach ($row_data as $row) {
                
                if (count($row) < 3) { //If row elements are less than 3
                    continue; // Skip this row and move to the next one
                }

                // Capitalize name and surname
                $name = trim(capitalize($row[0]));
                $surname = trim(capitalize($row[1]));
                $email = trim(strtolower($row[2])); // Convert email to lowercase
                
                // Validate the email address
                if (!validateEmail($email)) {
                    echo "Invalid email format for user {$row[2]}. Wont be inserted into table.\n";
                    $processed_data[] = array(
                        'name' => $name,
                        'surname' => $surname,
                        'email' => $email,
                        'error' => "Invalid email format for user {$row[2]}. Skipped insert into table."
                    );
                }
                
                else $processed_data[] = array(
                    'name' => $name,
                    'surname' => $surname,
                    'email' => $email
                );
           }
            echo "Processed Data:\n";
            echo "-------------------------------------------------------\n";
            echo "| Name       | Surname      | Email                   |\n";
            echo "-------------------------------------------------------\n";
            foreach ($processed_data as $row) {
                printf("| %-6s | %-7s | %-25s |\n", str_pad($row['name'], 10), str_pad($row['surname'], 10), str_pad(trim($row['email']), 10));
            }
            echo "-------------------------------------------------------\n";
           return $processed_data;
        }
       catch (Exception $e)
       {
        echo $e;
       }
    }

    function insertIntoTable($processed_data,$connection)
    {
        foreach ($processed_data as $data) {
            if (isset($data['error'])) {
                echo $data['error'] . "\n";
                continue;
            } else {
                $sql = "INSERT INTO users (name, surname, email) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE name = VALUES(name), surname = VALUES(surname)";
                // Prepare the statement
                $sql_statement = $connection->prepare($sql);
                $sql_statement->bind_param("sss", $data['name'], $data['surname'], $data['email']);

                if ($sql_statement->execute()) {
                    echo "Record inserted successfully for user {$data['email']}\n";
                } else {
                    echo "Error: Database insertion failed for user {$data['email']}. Error: " . $sql_statement->error . "\n";
            }
            }
        }   
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

    function checkDbValuesAndConnect($db_command_options_to_prompt)
    {
        foreach ($db_command_options_to_prompt as $option => $prompt) {
            promptForOption($option, $prompt);
        }
        $db_details = parse_ini_file(DB_DETAILS_FILE);
        $connection = connectToSql($db_details);
        return $connection;
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
            return $sqlconnection;
        }
        catch (Exception $e)
        {
            echo $e;
        }
    }

    while (True){

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

    // Check if database options are set through command-line arguments
    foreach ($db_command_options_to_prompt as $option => $prompt) {
        if (isset($command_options_to_prompt[$option])) {
            // Set the option value directly from command-line argument
            // Save the entered option to a file
            echo $command_options_to_prompt[$option];             
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

      // Function to update the file info into the users table
      // Prompt for --file
      if (isset($command_options_to_prompt['file'])) {
        $file_path = getcwd(). '\\'. $command_options_to_prompt['file'];
        if (file_exists($file_path))
        {
            // Read the file
            try{
                $file_contents = file_get_contents($file_path);
                if (isset($command_options_to_prompt['dry_run'])) {
                    echo "Dry run mode: Database won't be altered.\n";
                    $processed_data = processFileData($file_contents);
                } 
                else 
                {
                    $processed_data = processFileData($file_contents);
                    $connection = checkDbValuesAndConnect($db_command_options_to_prompt);
                    // Insert data into the database (if not in dry run mode)
                    echo "Inserting data into the database...\n";
                    insertIntoTable($processed_data,$connection);
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

    // Prompt for creating table
    if (isset($command_options_to_prompt['create_table'])) {
        checkDbValuesAndConnect($command_options_to_prompt);
    }

    exit;

} // While loop closing



?>