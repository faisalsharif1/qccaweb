<?php
/* Database credentials. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
defined('DB_SERVER') || define('DB_SERVER', 'localhost');
defined('DB_USERNAME') || define('DB_USERNAME', 'root');
defined('DB_PASSWORD') || define('DB_PASSWORD', 'Gladiater1');
defined('DB_NAME') || define('DB_NAME', 'MushtarikaAkhurwalCoalV5');

/* Attempt to connect to MySQL database */
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, 3306);

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}