<?php

// Define base path
define('BASE_PATH', '/');

define('DBHOST','localhost'); // Hostname
define('DBUSER','root'); // DB username
define('DBPASS','root'); // DB password
define('DBNAME','wescem'); // DB name

// Database configuration
$servername = DBHOST;
$username = DBUSER;
$password = DBPASS;
$dbname = DBNAME;

// Initialize global connection variable
global $conn;

try {
    // Create new PDO instance and set error mode
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    echo "Error: " . $e->getMessage();
}

$stmt = $conn->prepare("SELECT * FROM settings WHERE id= 1 LIMIT 1");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_OBJ);
$settings = $stmt->fetch();

//Wallet validation
define('WALLET_MINCH',$settings->wallet_min); // Wallet min characters
define('WALLET_MAXCH',$settings->wallet_max); // Wallet max characters

//Currency
define('CURSYM',$settings->currency_symbol); // Currency symbol
define('CURNAME',$settings->currency_name); // Currency name

//Format currency
function currencyFormat($value){
    return number_format($value, 8, '.','');
}