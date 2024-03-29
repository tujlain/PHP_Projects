<?php

// Loop through numbers from 1-100
for ($i = 1; $i <= 100; $i++) {
    // Construct output string
    $display_output = ($i % 3 == 0 ? 'foo' : '') . ($i % 5 == 0 ? 'bar' : '');

    // Output the constructed string or the number itself
    $output .= str_pad($display_output ? $display_output : $i, 6, ' ', STR_PAD_RIGHT);

    // Print comma after every output until less than 100
    if ($i < 100) {
        $output .= ", ";
    }

    // Add a newline after every 10 numbers
    if ($i % 10 == 0) {
        $output .= "\n";
    }

}

echo $output;
?>