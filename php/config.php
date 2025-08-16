<?php // php/config.php

/**
 * Main Configuration File
 *
 * This file defines constants for absolute paths and URLs to make the
 * application more portable and easier to manage.
 */

// --- Absolute File System Path ---
// This defines the physical path to your project's root folder on the server.
// (e.g., C:/xampp/htdocs/webapp)
define('ROOT_PATH', dirname(__DIR__));


// --- Base URL ---
// This dynamically determines the base URL of the project.
// It works whether your project is in the web root or a subfolder.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// If in a subfolder, SCRIPT_NAME will be /subfolder/api, so we need to go up one level.
// If in the root, it will be /, which is correct.
$base_path = (strpos($_SERVER['REQUEST_URI'], '/api/') !== false || strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) ? dirname($script_name) : $script_name;
$base_url = rtrim($protocol . $host, '/');

define('BASE_URL', $base_url.'/webapps');
