<?php
//    $dbHost = "ivcintdb1";
    // sql 2014 server
    $dbHost = "IEXDBLISTNR";
    $dbDatabase = "SARS";
    $dbUser = "ivcsars";
    $dbPass = "~7QM#pd?X*";

    // MSSQL database connection
    try {
        $dbConn = new PDO("sqlsrv:server=$dbHost;Database=$dbDatabase", $dbUser, $dbPass);
    } 
    catch (PDOException $e) {
        die ($e->getMessage());
    }
