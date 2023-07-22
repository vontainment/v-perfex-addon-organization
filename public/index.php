<?php
/*
 * Project: Perfex File Process
 * Author: Vontainment
 * URL: https://vontainment.com
 * File: index.php
 * Description: Process Perfex Addons
 */

require_once 'process_files.php';
require_once 'form-helper.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Module Sorter</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.0/min/dropzone.min.js"></script>
</head>

<body>
    <header>
        <h1>Perfex CRM Module Sorter</h1>
    </header>
    <div class="top-buttons">
        <button onclick="location.reload()">Refresh</button>
        <form action="index.php" method="post">
            <button type="submit" name="processFiles">Process Files</button>
        </form>
    </div>
    <div id="dropzoneContainer">
        <div id="dropzone" class="dropzone">
            <span class="dz-message">Drag and drop files here or click to upload</span>
        </div>
    </div>
    <div id="message-container"></div>
    <div class="row">
        <div class="column">
            <h2>Processing</h2>
            <?php
            $dir = './process/';
            // List the files in the process directory
            $files = scandir($dir);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    echo '<p>' . $file . '</p>';
                }
            }
            ?>
        </div>
        <div class="column">
            <h2>Completed</h2>
            <?php
            $completed_dir = './completed/';
            // List the files in the completed directory
            $files = scandir($completed_dir);
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    echo '<p><a href="' . $completed_dir . $file . '" download>' . $file . '</a></p>';
                }
            }
            ?>
        </div>
    </div>

    <script>
        Dropzone.autoDiscover = false;
        $(document).ready(function() {
            var myDropzone = new Dropzone("#dropzone", {
                url: "/", // replace with your server-side processing script
                paramName: "plugin_file",
                maxFiles: 6,
                maxFilesize: 200, // in MB
                acceptedFiles: ".zip,application/zip,application/x-zip-compressed,multipart/x-zip",
                autoProcessQueue: true,
                parallelUploads: 6,
                init: function() {
                    this.on("success", function(file, response) {
                        var successMsg = $('<div class="success-message">Successfully uploaded file: ' + file.name + '</div>');
                        $('#message-container').append(successMsg);
                    });

                    this.on("error", function(file, errorMessage) {
                        var errorMsg = $('<div class="error-message">Error uploading file: ' + file.name + '</div>');
                        $('#message-container').append(errorMsg);
                    });
                }
            });
        });
    </script>
</body>

</html>