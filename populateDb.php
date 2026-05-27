<?php

require_once 'pdo.php';

$csvFile = 'countries.csv';
$handle = fopen($csvFile, 'r');

if ($handle) {
    while ( ($csvData = fgetcsv($handle, 250, ",")) !== FALSE ) {
        // Get the data for each row
        $pk = intval($csvData[4]);
        $country = $csvData[0];
        $capital = ($csvData[1] != "0") ? $csvData[1] : NULL;
        $countryCode = $csvData[2];
        $hint = ($csvData[3] != "") ? $csvData[3] : NULL;

        // Write it to the database
        var_dump($pk, $country, $capital, $countryCode, $hint);
        echo '<br><br>';
        $bound = array(
            ':pk' => $pk,
            ':ct' => $country,
            ':cp' => $capital,
            ':cc' => $countryCode,
            ':ht' => $hint 
        );
        
        $stmt = $pdo->prepare('SELECT 1 FROM Countries WHERE pk = :pk');
        $stmt->execute(array(':pk' => $pk));
        if ( $stmt->fetchColumn() ) {
            $stmt = $pdo->prepare('UPDATE Countries
                SET country=:ct, capital=:cp, code=:cc, hint=:ht) 
                WHERE pk=:pk');
            $stmt->execute($bound);
        } else {
            $stmt = $pdo->prepare('INSERT INTO Countries
                (pk, country, capital, code, hint) 
                VALUES ( :pk, :ct, :cp, :cc, :ht )');
            $stmt->execute($bound);
        }
    }
    fclose($handle);
}

/* 
Before running this file create the following database and table.

Create Countries data table in database. Below is for MySQL
!!! Set a username and password to access the database

CREATE DATABASE flags DEFAULT CHARACTER SET utf8;
CREATE USER 'username'@'localhost' IDENTIFIED BY 'enterYourPassword';
GRANT ALL ON flags.* TO 'username'@'localhost'';
CREATE USER 'username'@'127.0.0.1' IDENTIFIED BY 'enterYourPassword';
GRANT ALL ON flags.* TO 'username'@'127.0.0.1';

Create table for countries / primary key and data will be populated from CSV

CREATE TABLE Countries (
   pk SMALLINT NOT NULL,
   country VARCHAR(128) NOT NULL,
   capital VARCHAR(128),
   code VARCHAR(12) NOT NULL,
   hint VARCHAR(128),
   PRIMARY KEY(pk),
   UNIQUE(pk)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

*/