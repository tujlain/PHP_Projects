<?php


    $command_options_to_prompt = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);
    //  // Array of the command line options prompted to the user for the database
    //  $db_command_options_to_prompt = [
    //     'u' => 'MySQL username',
    //     'p' => 'MySQL password',
    //     'h' => 'MySQL host',
    //     'port' => 'MySQL port number',

    // ];

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

    // // To check if db values are set or not.
    // foreach ($db_command_options_to_prompt as $option => $prompt) {
    //     if (isset($command_options_to_prompt[$option])) {
    //         $command_options_to_prompt[$option] = trim(fgets(STDIN));
    //     }
    // }

    // // To check if create table is set and check for all db values set or not
    // if (isset($command_options_to_prompt['create_table']))
    // {
    //     // Loop through the options and prompt the user if they are not set
    //     foreach ($db_command_options_to_prompt as $option => $prompt) {
    //         if (!isset($command_options_to_prompt[$option])) {
    //             echo "Enter $prompt: ";
    //             $command_options_to_prompt[$option] = trim(fgets(STDIN));
    //         }
    //     }
    // }


    define('DB_DETAILS_FILE', getcwd() . '\db_details.txt');

    // Array of database options to prompt for
    $db_command_options_to_prompt = [
        'u' => 'MySQL username',
        'p' => 'MySQL password',
        'h' => 'MySQL host',
    ];

    // Function to prompt for missing options
    function promptForOption($option, $prompt) {
        global $command_options_to_prompt;

        if (!getDbInfo($option))
        {
            echo "Enter $prompt: ";
            $command_options_to_prompt[$option] = trim(fgets(STDIN));
            writeDBInfo($option, $command_options_to_prompt);
        }
        // if (!isset($command_options_to_prompt[$option])) {
        //     echo "Enter $prompt: ";
        //     $command_options_to_prompt[$option] = trim(fgets(STDIN));
        //     writeDBInfo($option, $command_options_to_prompt);
        //     // Save the entered option to a file
        // }
    }

    function writeDBInfo($option, $command_options_to_prompt)
    {
        // $db_details = parse_ini_file(DB_DETAILS_FILE);
        // file_put_contents(DB_DETAILS_FILE, "$option={$command_options_to_prompt[$option]}\n", FILE_APPEND);

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
        foreach ($db_details as $opt => $value) {
            $content .= "$opt=$value\n";
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


    while (True){
    // Check if db details file exists
    // if (file_exists(DB_DETAILS_FILE)) {
    //     // Read db details from the file
    //     $db_details = parse_ini_file(DB_DETAILS_FILE);
    //     foreach ($db_details as $option => $value) {
    //         // Override options with saved values
    //         $command_options_to_prompt[$option] = $value;
    //     }
    // }

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
                // if (filesize($DB_DETAILS_FILE) > 0)
                // {
                //     
                //     foreach ($db_details as $option => $value) {
                //         // Override options with saved values
                //         $command_options_to_prompt[$option] = $value;
                //         echo $command_options_to_prompt[$option];
                //         echo $option;
                //         }
                //     exit;
                // }

            }
            catch (Exception $e)
            {
                echo $e;
            }
            
        }
    }

    // Prompt for missing database options if create_table is set
    if (isset($command_options_to_prompt['create_table'])) {
        foreach ($db_command_options_to_prompt as $option => $prompt) {
            promptForOption($option, $prompt);
        }
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