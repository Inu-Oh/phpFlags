# phpFlags
Simple PHP flag quiz app.

A geography quiz Web app written in PHP, testing knowledge of flags, country and capital city names. The app randomly chooses between four types of quizzes, then chooses a random question from each quiz question list, just under one thousand questions in total.

See below for SQL and PHP code for use to set up quiz question data on PostgreSQL.

## Quiz types
- Guess country from flag
- Guess capital from flag
- Guess capital from country
- Guess country from capital

## Features
All quiz data is currenty stored in the 'countries.php' array file, from which questions are accessed by the main view pages using jQuery to embed JSON data in Handlebars stript templates. The app is now writting fully in PHP. I have eliminated most JavaScript elments.

## Planned development
### Quiz data storage
The quiz data is now stored in database and retrieved with PDO for each question. See earlier version for a pure PHP array implementation.
### User progress
Choosing an SQL schema to store user learning progress on all quiz questions. Planning how the user data will be best set up together with the quiz data.

## Gallery
### Start Quiz - sample question for Guess country from capital city name
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/c78b8418-801a-4195-b5a0-53d4f5da5166" />

### Quiz Question - Guess country from flag
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/a3af0c73-5ec2-48a0-b080-2e1b6daf72d3" />

### Quiz question - Guess capital city name from flag
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/5c116a90-4771-4ef0-a4a2-7ca1d503c06d" />

### Quiz question - Guess country from capital city name
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/4f21d0bb-4085-4bc9-8a20-cdbd67dc0809" />

### Feedback on country name guess from flag
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/adf907ab-606a-45f2-a612-7271a48077e2" />

### Feedback on capital city name guess from country - showing perfect score
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/68a6dab0-00d2-4d1a-a683-13bf15f36e42" />

### Feedback on capital city name guess from flag - misspelling feedback
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/d74849ed-b497-406d-9e6c-a5261025f21e" />

### Feedback on country name guess from flag - wrong guess
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/69f4ec41-331c-4fff-86b2-033c96d63ec6" />

### Feedback on country name guess from capity city name - wrong guess
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/763845b8-1b76-413c-bfb9-44d47f9313f2" />

## Setup Quiz Data in Database from CSV

Before running this file create the following database and table.

Create a database to store the quiz data.
Set a username and password to access the database.

```
CREATE USER zephyr WITH PASSWORD '2wsx@WSXZAQ!zaq1';
CREATE DATABASE flags WITH OWNER 'zephyr' ENCODING 'UTF8';
```

Create table for countries / primary key and data will be populated from CSV

```
CREATE TABLE countries (
   pk SMALLINT,
   country VARCHAR(128) NOT NULL,
   capital VARCHAR(128),
   code VARCHAR(12) NOT NULL,
   hint VARCHAR(128),
   PRIMARY KEY(pk),
   UNIQUE(pk)
);
```

Create a pdo.php file with the following code, setting the username to match the values you used to create the database.

```
<?php

if ( file_exists('../config.php') ) {
    include_once('../config.php');
}

if ( ! isset( $pdo ) ) {
    try {
	    $pdo = new PDO(
			'pgsql:host=localhost;dbname=flags', 'userName', 'passWord',
			[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
		);
	} catch (PDOException $e) {
		die("Connection failed: " . $e->getMessage());
	}
}
```

Create a php file with the following code and visit the page in your browser to run it. 
IMPORTANT: Delete the file after populating the database from the CSV file to prevent SQL injection. Edit the CSV file and rerun the file to edit the database. (The file name does not matter.)

```
<?php

require_once __DIR__ . '/src/pdo.php';

$csvFile = __DIR__ . '/static/countries.csv';
$handle = fopen($csvFile, 'r');

if ($handle) {
    while ( ($csvData = fgetcsv($handle, 250, ",")) !== FALSE ) {
        // Get the data for each row of the CSV file
        $pk = intval( $csvData[4] );
        $country = $csvData[0];
        $capital = ( $csvData[1] != "0" ) ? $csvData[1] : NULL;
        $countryCode = $csvData[2];
        $hint = ( $csvData[3] != "" ) ? $csvData[3] : NULL;

        // Write each row of quiz data to the database
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
```

Create a table to store user data. SQL for PostreSQL.

```
CREATE TABLE users (
	user_id SERIAL,
	username VARCHAR(32) NOT NULL UNIQUE,
	email VARCHAR(128) NOT NULL UNIQUE,
	pw_hash BYTEA NOT NULL,
	salt VARCHAR(64) NOT NULL,
	is_admin BOOLEAN DEFAULT FALSE,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	PRIMARY KEY (user_id)
);
```