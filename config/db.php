<?php
/**
 * Database Connection File
 * Bus Pass Management System
 * Uses MySQLi procedural connection
 */

// Start session across all pages that include db.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host     = "localhost";
$username = "root";
$password = "";
$database = "bus_pass_db";

// Establish MySQLi connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("<div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px;'>
            <h3>Database Connection Error</h3>
            <p>Could not connect to the database <strong>" . htmlspecialchars($database) . "</strong>.</p>
            <p>Please ensure that:</p>
            <ul>
                <li>XAMPP MySQL is running.</li>
                <li>The database <code>bus_pass_db</code> has been imported using phpMyAdmin.</li>
            </ul>
            <p><strong>MySQL Error:</strong> " . mysqli_connect_error() . "</p>
         </div>");
}

// Set character set to utf8mb4 for complete unicode support
mysqli_set_charset($conn, "utf8mb4");
?>
