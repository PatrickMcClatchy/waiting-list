<?php
// Make sure Composer is installed on the server.
if (file_exists('../composer.json')) {
    echo "Running composer install...\n";

    // Run Composer install (make sure PHP CLI has permission to execute Composer)
    $output = shell_exec('composer install');
    
    // Check for errors
    if ($output) {
        echo "<pre>$output</pre>";
    } else {
        echo "Composer installation completed successfully.";
    }
} else {
    echo "No composer.json file found!";
}
?>
