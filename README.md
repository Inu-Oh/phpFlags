# phpFlags
A flag quiz Web app written in php

This is in early development. The GUI is not worth sharing at this time.

The app chooses randomly chooses between four types of quizzes, then chooses a random question from each.
- Guess country from flag
- Guess capital from flag
- Guess capital from country
- Guess country from capital

The app runs currently with no database needed to store quiz question details. All quiz data is stored in the 'countries.json' file as JSON data, from which questions are accessed in PHP files using jQuery with the help of Handlebars templates.

## Planned development
After working out the feedback and GUI, I will add SQL to store user data that will track learning progress for each country and enable users to return and review their what they have learned.
