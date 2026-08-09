# phpFlags
Simple PHP flag quiz app.

>  [!CAUTION]
>
>  This app is still in development and only suitable for testing and development

A geography quiz Web app written in PHP, testing knowledge of flags, country and capital city names. The app randomly chooses between four types of quizzes, then chooses a random question from each quiz question list, just under one thousand questions in total.

## Quiz question types
Quiz cards are selected in random order
- Guess country from flag
- Guess capital from flag
- Guess capital from country
- Guess country from capital

## Quiz modes
- Learn - trains user on new quiz cards selected in random order - anon and account users
- Practice - strengthens user skill by focusing on weakest quiz cards - requires account
- Review - refreshes user memory of leaned quiz cards in random order - requires account

## Features
All quiz data is currently stored in the 'countries.php' array file, from which question and feedback data are accessed by the main view pages using jQuery to embed JSON data in Handlebars script templates. The app is now written fully in PHP, using minimal JavaScript.

PostgreSQL database stores all quiz information as well as user data and user progress data for each quiz question.

Left side navigation, card scoreboard, and right side info pane provide context to quiz progression through cards selected depending on quiz mode.

When anonymous user logs in current session data is used to update any cards the user has tested and automatically updates to the user progress data in the database.

Scroll below gallery for PostgreSQL and PHP code implementation of user, user progress, and quiz question data.

### In development
- Finalize functionality
- Review security features
- Write tests

## Gallery

### Full Main Quiz Page
Practice quiz mode question with logged in user - showing left navigation, quiz mode scoreboard and user right info pane
- navigation with links to logout or switch quiz modes
<img width="1167" height="826" alt="image" src="https://github.com/user-attachments/assets/a6b5af92-8753-4e55-84f6-198f2c4f7c11" />


### Full Feedback Page
Review quiz mode with logged in user - showing left and right info panes and quiz mode scoreboard
- navigation disabled during feedback
<img width="1167" height="826" alt="image" src="https://github.com/user-attachments/assets/1b0998d9-a530-46c5-a93d-ebcf59490b7b" />

### Full Anonymous Quiz Page
Learn quiz mode with anonymous user - showing scoreboard for perfect quiz results and similar left navigation and right info panes
- navigation links to log in and out
- only learn mode is available to anonymous users
- info pane has similar information 
<img width="1257" height="826" alt="image" src="https://github.com/user-attachments/assets/6f4aa029-1461-4047-80e0-2256efc6b702" />

### Start Quiz - Guess country name from flag - anonymous
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/f346fd1d-a421-4c58-a5d2-15b281b9a27a" />

### Quiz Question - Guess capital city from flag - example of perfect score - anonymous
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/c410af8c-3a47-4c70-b283-9464bf393473" />

### Quiz question - Guess capital city name from country - review mode
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/2c129aaf-e25b-41d1-8bbb-0243dfcbb9fa" />

### Quiz question - Guess country from capital city name - logged in user
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/41640c87-2757-4277-bb18-04309694fc84" />

### Feedback on country name guess from flag
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/adf907ab-606a-45f2-a612-7271a48077e2" />

### Feedback on capital city name guess from country
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/0544831b-1c48-43e7-b741-b726759e74a2" />

### Feedback on capital city name guess from flag - misspelling feedback
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/d74849ed-b497-406d-9e6c-a5261025f21e" />

### Feedback on country name guess from flag - wrong guess - practice mode
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/f12fddaa-07cf-4de5-afee-d70114116c08" />

### Feedback on country name guess from capital city name - wrong guess
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/12e4a77d-eeb7-4bb0-a0d0-0ccf0d9bab05" />

### Error message shown when user enters country instead of capital
- Similar message given when user enters capital instead of country
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/993284ff-6b40-4289-8140-d752e5ce0e8c" />

### Error message shown when user does not enter a value
<img width="660" height="700" alt="image" src="https://github.com/user-attachments/assets/712e6180-1b5e-4407-b24a-e2c3525c4f6a" />

## Add config.php

For this app to work, it needs a config.php file that will start a secure session environment. For example, this video provides config.php code : https://www.youtube.com/watch?v=FbLYsTHJKDw

## Setup Quiz Data in Database from CSV

Before running this file create the following database and tables. All SQL is written for PostgreSQL.

Create a database to store the quiz data.
Set a username and password to access the database.

```
CREATE USER username WITH PASSWORD 'password';
CREATE DATABASE flags WITH OWNER 'username' ENCODING 'UTF8';
```

Create table for countries / primary key and data will be populated from CSV.

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
			'pgsql:host=localhost;dbname=flags', 'username', 'password',
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

Create a table to store user data.

```
CREATE TABLE users (
	user_id SERIAL,
	username VARCHAR(32) NOT NULL UNIQUE,
	email VARCHAR(128) NOT NULL UNIQUE,
	pw_hash VARCHAR(255) NOT NULL,
	is_admin BOOLEAN DEFAULT FALSE,
	created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	PRIMARY KEY (user_id)
);
```

Create a table to store user progress on each quiz question. 

```
CREATE TABLE progress (
	user_id INTEGER REFERENCES users(user_id) ON DELETE CASCADE,
	country_id SMALLINT REFERENCES countries(pk) ON DELETE CASCADE,
	quiz_id SMALLINT REFERENCES countries(pk) ON DELETE CASCADE,
	test_count SMALLINT DEFAULT 0,
	correct_count SMALLINT DEFAULT 0,
	updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
	PRIMARY KEY (user_id, country_id, quiz_id)
);
```
