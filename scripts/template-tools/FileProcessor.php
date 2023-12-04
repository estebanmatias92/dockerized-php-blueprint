<?php

// Get recursiveGlob
require_once __DIR__ . "/utilities.php";

class FileProcessor {
    private $directory;
    private $fileNames;
    private $isRecursive;
    private $excludedDirectories = [];

    /**
     * @param array $fileNames An array with the name/expression of the files you want to be included in the processing.
     * @param string $directory Set the path string, by default is the current folder.
     * @param bool $isRecursive Set this to true if you want to include the files from subdirectories too.
     * 
     * @return void
     */
    public function __construct(array $fileNames, string $directory = __DIR__, bool $isRecursive = false) {
        $this->setFileNames($fileNames);
        $this->setDirectory($directory);
        $this->isRecursive = $isRecursive;
    }

    // Setter for $directory
    public function setDirectory(string $directory) {
        // Ensure the directory string ends with a DIRECTORY_SEPARATOR
        $this->directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    // Setter for $fileNames
    public function setFileNames(array $fileNames) {
        $this->fileNames = $fileNames;
    }

    // Setter for $excludedDirectories
    public function setExcludedDirectories(array $excludedDirectories) {
        $this->excludedDirectories = array_map(function($dir) {
            return $this->directory . rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }, $excludedDirectories);
    }

    // Replace all the placeholders in all the files
    public function processFiles(array $replacements) {
        $files = $this->getAllFiles();
        $pattern = $this->createPattern($replacements);

        foreach ($files as $file) {
            //echo "Filename: {$file}\n"; // Borrar
            if ($this->processFile($file, $pattern, $replacements)) {
                yield $file;
            }
        }        
    }

    /**
     * Retrieves all file paths based on the specified file names.
     * 
     * @return array An array of file paths.
     */
    private function getAllFiles() {
        $list = [];

        foreach ($this->fileNames as $fileName) {
            $filePaths = $this->findFiles($fileName);
            $list = array_merge($list, $filePaths);
        }

        return $list;
    }

    /**
     * Finds files based on a given pattern. It decides whether to perform a recursive search
     * or a regular glob search based on the value of $this->isRecursive.
     * 
     * @param string $pattern The file name or pattern to search for.
     * 
     * @return array An array of file paths that match the given pattern.
     */
    private function findFiles(string $pattern) {
        if ($this->isRecursive) {
            // Perform a recursive search if $this->isRecursive is true.
            return recursiveGlob($this->directory, [$pattern], $this->excludedDirectories);
        } else {
            // Perform a regular glob search otherwise.
            return glob($this->directory . $pattern);
        }
    }

    /**
     * Build and return a regular expresion with the placeholders
     * 
     * @param array $replacements Set an associative array with the patterns as Keys and the new values to replace as Values.

     * @return string Returns a regular expression with all the patterns/placeholders concatenated.
     */
    private function createPattern(array $replacements) {
        $patterns = array_map(function($key) {
            return preg_quote($key, "/");
        }, array_keys($replacements));
        
        return "/" . implode('|', $patterns) . "/";
    }

    // Replace the placeholders in the file with the new strings, return if it was successful or not
    private function processFile(string $file, string $pattern, array $replacements) {
        $content = file_get_contents($file);
        if ($content === false) {
            return false; // Manejo de error de lectura
        }

        if (preg_match($pattern, $content)) {
            $modifiedContent = preg_replace_callback($pattern, function($matches) use ($replacements) {
                return $replacements[$matches[0]] ?? $matches[0];
            }, $content);

            if (file_put_contents($file, $modifiedContent) === false) {
                return false; // Manejo de error de escritura
            }

            return true;
        }

        return false;
    }
}

