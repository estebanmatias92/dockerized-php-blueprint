<?php 
define("PROJECT_NAME", getenv("PROJECT_NAME")); // Get the name for the app

require_once __DIR__ . '/utilities.php'; // Needed for the string parsing function
require_once __DIR__ . '/FileProcessor.php'; // Include the FileProcessor class

// Menu Screen

// Asking the customizations
echo "Enter the name for the app [" .PROJECT_NAME . "]: ";
$newNamespace = trim(fgets(STDIN));
// Comprobar si la entrada está vacía
if (empty($newNamespace)) {
    $newNamespace = PROJECT_NAME;
}
// Parsear con toPascalCase en cualquier caso
$newNamespace = toPascalCase($newNamespace);
// Developer's data
echo "Your name: ";
$authorName = trim(fgets(STDIN));
echo "Your email: ";
$authorEmail = trim(fgets(STDIN));

// Preparing directory, files and patterns to search and replace
$directory = dirname(dirname(__DIR__));
$fileNames = ['*.php', 'composer.json']; // Files to process, you can use expressions
$replacements = [
    "{{ placeholder.namespace }}" => $newNamespace,
    "{{ placeholder.authors.name }}" => $authorName,
    "placeholder.authors@email" => $authorEmail
]; // Patterns and their replacements

// Instantiate the FileProcessor
$processor = new FileProcessor($fileNames, $directory, true);
$processor->setExcludedDirectories(['config', 'scripts', 'vendor/']);

// Process the files
$modifiedFiles = $processor->processFiles($replacements);


// Showing the results
echo "\nUpdated files:\n";

foreach ($modifiedFiles as $file) {
    echo "    - " . $file . "\n";
}
