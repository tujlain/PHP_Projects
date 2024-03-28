<?php


    $command_options_to_prompt = getopt("u:p:h:", ["file:", "create_table", "dry_run", "help"]);

     // Array of the command line options prompted to the user for the database
     $db_command_options_to_prompt = [
        'u' => 'MySQL username',
        'p' => 'MySQL password',
        'h' => 'MySQL host',
        'port' => 'MySQL port number',

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

    // To check if db values are set or not.
    // if (!isset($command_options['u']) || !isset($command_options['p']) || !isset($command_options['h'])) {
    // }


    // To check if create table is set and check for all db values set or not
    if (isset($command_options_to_prompt['create_table']))
    {
        // Loop through the options and prompt the user if they are not set
        foreach ($db_command_options_to_prompt as $option => $prompt) {
            if (!isset($command_options_to_prompt[$option])) {
                echo "Enter $prompt: ";
                $command_options_to_prompt[$option] = trim(fgets(STDIN));
            }
        }
    }
//     $command_options = getopt("u:p:h:");

//     // Check if required options are provided
//     if (!isset($command_options['u']) || !isset($command_options['p']) || !isset($command_options['h'])) {
//         echo "Error: Missing required command-line options.\n";
//     exit(1);
// }
?>