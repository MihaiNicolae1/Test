nanop<?php

set_time_limit(0);

$options = getopt('', array('help', 'file:', 'type:', 'iterator:'));
$rules = "\nUsage:\n\nphp scriptFor6704.php --file [fileName].csv --type [type]\n\n"
    . "Options:"
    . "\n--type\t\tProvide the type of delete (soft or hard)"
    . "\n--file\t\tProvide file that contains skus and site names"
    . "\n--help\t\tDisplay this help\n\n";
foreach (array_keys($options) as $option) {
    switch ($option) {
        case 'help':
            echo $rules;
            exit(1);
        case 'file':
        case 'type':
        case 'iterator':
            if (!isset($options['iterator']) || !is_numeric($options['iterator'])) {
                echo "Iterator is not provided or is not numeric";
                exit(1);
            }
            if (!isset($options['type']) || !isset($options['file'])) {
                echo "The type/file parameter was not specified, or no value for it was provided. Run the script with --help to see the usage options\n";
                exit(1);
            }
            $fileName = $options['file'];
            $filePart = pathinfo($options['file']);
            if (array_key_exists('extension', $filePart) && $filePart['extension'] !== 'csv' || empty($filePart['extension'])) {
                echo "Either the file was not specified or the extension of it is missing/different than csv\n";
                exit(1);
            }
            if (!$options['type']) {
                echo "Please specify a type from the available ones: soft or hard\n";
                exit(1);
            }
            if ($options['type'] != "soft" && $options['type'] != "hard") {
                echo "Wrong type specified. Expected to receive soft or hard!\n";
                exit(1);
            }
            break;
        default:
            echo "Invalid key specified or empty value, run the script with --help to see the usage options\n";
            exit(1);
    }
}

for ($i = 0; $i <= $options['iterator']; $i++) {
    echo 'Script iteration number ' . $i . ' from file: '. $options['file'] . ' and type ' . $options['type'] . PHP_EOL;
    file_put_contents('test_generated_file_' . $i . '.txt' , $options['file']);
    sleep(1);
}

exit(0);
