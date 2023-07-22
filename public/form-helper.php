<?php
/*
 * Project: Perfex File Process
 * Author: Vontainment
 * URL: https://vontainment.com
 * File: form-helper.php
 * Description: Process Perfex Addons
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['plugin_file'])) {
        $upload_dir = './process/';
        $allowed_extensions = ['zip'];

        // Handling multiple files
        $uploaded_files = $_FILES['plugin_file'];
        $total_files = is_array($uploaded_files['name']) ? count($uploaded_files['name']) : 1;

        for ($i = 0; $i < $total_files; $i++) {
            $file_name = is_array($uploaded_files['name']) ? $uploaded_files['name'][$i] : $uploaded_files['name'];
            $file_tmp = is_array($uploaded_files['tmp_name']) ? $uploaded_files['tmp_name'][$i] : $uploaded_files['tmp_name'];
            $file_error = is_array($uploaded_files['error']) ? $uploaded_files['error'][$i] : $uploaded_files['error'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $destination = $upload_dir . $file_name;

            // Check for file errors and allowed extensions
            if ($file_error !== UPLOAD_ERR_OK || !in_array($file_extension, $allowed_extensions)) {
                // You can log the error here or return a response to client-side
            } else {
                // Move the uploaded file to the process directory
                if (!move_uploaded_file($file_tmp, $destination)) {
                    // You can log the error here or return a response to client-side
                }
            }
        }
    }

    if (isset($_POST['processFiles'])) {
        process_files();
    }
}
