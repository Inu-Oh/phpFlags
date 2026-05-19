# phpFlags
Simple PHP flag quiz app.

A geography quiz Web app written in PHP, testing knowledge of flags, country and capital city names. The app randomly chooses between four types of quizzes, then chooses a random question from each quiz question list, just under one thousand questions in total.

## Quiz types
- Guess country from flag
- Guess capital from flag
- Guess capital from country
- Guess country from capital

## Features
All quiz data is currenty stored in the 'countries.php' array file, from which questions are accessed by the main view pages using jQuery to embed JSON data in Handlebars stript templates. The app is now writting fully in PHP. I have eliminated most JavaScript elments.

## Planned development
### Quiz data storage
Given concerns that storing quiz data as an array may not be the most secure choice, I will soon either make countries.php read-only or save it to a database and use PDO to get quiz question data for the GUI.
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
