<?php

require_once 'connection.php';
$host = jqbd_server;
$db   = jqbd_name;
$charset = defined("CHARSET")?CHARSET:'utf8';
$charset = str_replace('-', '', $charset);
define('DB_DSN',"mysql:host=$host;dbname=$db;charset=$charset");
define('DB_USER', jqbd_user);     // Your MySQL username
define('DB_PASSWORD', jqbd_password); // ...and password
define('ABSPATH', dirname(__FILE__).'/');