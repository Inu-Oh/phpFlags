# phpFlags
A flag quiz Web app written in php

This is in early development. The GUI is not worth sharing at this time.

The app randomly chooses between four types of quizzes, then chooses a random question from each quiz question list.
- Guess country from flag
- Guess capital from flag
- Guess capital from country
- Guess country from capital

The app runs currently with no database needed to store quiz question details. All quiz data is stored in the 'countries.json' file as JSON data, from which questions are accessed in PHP files using jQuery with the help of Handlebars templates.

## Planned development
After working out the feedback and GUI, I will add SQL to store user data that will track learning progress for each country and enable users to return and review their what they have learned.

## Gallery

### Start Quiz - sample question for Guess capital city name from country
!["Quiz start page with random question selected"](image.png)

### Quiz Question - Guess country from flag
!["Quiz question asking for name of country based on image of its flag"](image-3.png)

### Quiz question - Guess capital city name from flag
!["Quiz question asking for name of capital city based on country's flag"](image-9.png)

### Quiz question - Guess country from capital city name
!["Quiz question asking for name of country based on its capital city name"](image-2.png)

### Feedback on country name guess from flag
!["Quiz feedback on correct guess of country name based on its flag"](image-4.png)

### Feedback on capital city name guess from country - showing perfect score
!["Feedback on correct answer for capital city name showing a perfect score"](image-1.png)

### Feedback on capital city name guess from flag - misspelling 
!["Feedback on correct answer for capital city name showing pointing out a spelling mistake"](image-6.png)

### Feedback on country name guess from flag - wrong guess
!["Feedback on wrong answer for country name based on flag"](image-7.png)

### Feedback on country name guess from capity city name - wrong guess
!["Feedback on wrong answer for country name based on capital city name"](image-8.png)