<?php

require_once 'config.php';

function connectDB()
{
    $connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    if ($connection->connect_error) {
        die('Error de conexión: ' . $connection->connect_error);
    }

    return $connection;
}

function generateLicenseKey()
{
    $length = 16;
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $key = '';

    for ($i = 0; $i < $length; $i++) {
        $key .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $key;
}
