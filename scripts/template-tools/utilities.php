<?php

/**
 * Make Glob Great Again.
 * 
 * @param string $directory Full path
 * @param array $patterns Files or regular expression
 * @param array $excludedDirectories Directories to exclude
 */
function recursiveGlob(string $directory, array $patterns, array $excludedDirectories = []) {
    $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $files = [];

    // Check if the current directory is excluded
    if (in_array($directory, $excludedDirectories)) {
        return [];
    }

    // Look for matching files in the current directory
    foreach ($patterns as $pattern) {
        $files = array_merge($files, glob($directory . $pattern));
    }

    // Search in sub-directories
    foreach (glob($directory . '*', GLOB_ONLYDIR) as $subdir) {
        $files = array_merge($files, recursiveGlob($subdir, $patterns, $excludedDirectories));
    }

    return $files;
}


/**
 * Like the name of the function implies, just parses any string to PascalCase.
 * 
 * @param string $string Provide a simple string!
 */
function toPascalCase(string $string) {
    // Transforms any connector to spaces
    $string = str_replace(['-', '_'], ' ', $string);
    // Trims any spaces and capilizes every word
    $string = str_replace(' ', '', ucwords($string));

    return $string;
}