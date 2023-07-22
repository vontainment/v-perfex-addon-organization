<?php
/*
 * Project: Perfex File Process
 * Author: Vontainment
 * URL: https://vontainment.com
 * File: process_helper.php
 * Description: Process Perfex Addons
 */

function process_files()
{
    $process_dir = './process/';
    $tmp_dir = './tmp/';
    $completed_dir = './completed/';
    $errors_dir = './errors/';

    while (true) {
        // Get the next zip file in the process directory
        $zip_file = nextZipFile($process_dir);

        // Exit the loop if there are no more zip files
        if (is_null($zip_file) || $zip_file === false) {
            break;
        }

        // Remove the contents of the tmp directory before processing
        removeDirectoryContents($tmp_dir);

        // Extract the zip file to the tmp directory
        $extracted_folder = extractZipFile($zip_file, $tmp_dir);

        // Check for a __MACOSX folder in the tmp directory and remove it
        removeDirectory($tmp_dir . '__MACOSX');

        // Check if an extracted folder was found
        if ($extracted_folder === false) {
            moveZipFile($zip_file, $errors_dir);
            continue;
        }

        // Check if the corresponding PHP file exists in the extracted folder
        $php_file = $tmp_dir . $extracted_folder . '/' . $extracted_folder . '.php';
        if (!file_exists($php_file)) {
            moveZipFile($zip_file, $errors_dir);
            continue;
        }

        // Read the PHP file and find the version
        $file_contents = file_get_contents($php_file);
        preg_match('/Version: (\d+(?:\.\d+)*)/', $file_contents, $matches);
        if (isset($matches[1])) {
            // Rename the zip file with the version number
            $new_name = str_replace('_', '-', $extracted_folder) . '-' . $matches[1] . '.zip';

            // Check if a zip file with the same name already exists in the completed directory
            if (file_exists($completed_dir . $new_name)) {
                // Skip processing and delete the zip file from the process directory
                unlink($zip_file);
                continue;
            }

            rename($zip_file, $tmp_dir . $new_name);

            // Move the renamed zip file to the completed directory
            moveZipFile($tmp_dir . $new_name, $completed_dir);
        } else {
            moveZipFile($zip_file, $errors_dir);
        }

        // Remove the contents of the tmp directory
        removeDirectoryContents($tmp_dir);
    }
}

function nextZipFile($dir)
{
    $zip_files = glob($dir . '*.zip');
    return array_shift($zip_files);
}

function extractZipFile($zip_file, $tmp_dir)
{
    $zip = new ZipArchive;
    $res = $zip->open($zip_file);
    if ($res === true) {
        $zip->extractTo($tmp_dir);
        $zip->close();

        $extracted_folder = '';
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($tmp_dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) {
                $extracted_folder = $file->getFilename();
                break;
            }
        }

        return $extracted_folder;
    } else {
        return false;
    }
}

function moveZipFile($zip_file, $dest_dir)
{
    $new_path = $dest_dir . basename($zip_file);
    rename($zip_file, $new_path);
}

function removeDirectory($path)
{
    if (is_dir($path)) {
        $files = new \FilesystemIterator($path);
        foreach ($files as $file) {
            if ($file->isDir()) {
                removeDirectory($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($path);
    }
}

function removeDirectoryContents($path)
{
    $files = glob(rtrim($path, '/') . '/*');
    foreach ($files as $file) {
        removeDirectory($file);
    }
}
