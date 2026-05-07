<?php

// Get the next quiz question and save it to session
function getQuestion() {
    $quizzes = array('flagCountry', 'flagCapital', 'countryCapital', 'capitalCountry');
    $randomQuiz = $quizzes[array_rand($quizzes)];
    $chosenList = $_SESSION[$randomQuiz];
    $randomQuestion = array_rand($chosenList);
    $_SESSION['nextQuestion'] = $randomQuestion;
    unset($chosenList[$randomQuestion]);
    $_SESSION[$randomQuiz] = $chosenList;
}

// Set up all quiz questions to session at start or restart
function setQuestions() {
    list($countryIntList, $capitalIntList) = quizLists();

    $_SESSION['flagCountry'] = shuffle($countryIntList);
    $_SESSION['flagCapital'] = shuffle($capitalIntList);
    $_SESSION['countryCapital'] = shuffle($capitalIntList);
    $_SESSION['capitalCountry'] = shuffle($capitalIntList);
    $_SESSION['quizIsSet'] = TRUE;
}

// Create lists of integers for use as quiz lists
function quizLists() {
    $jsonData = file_get_contents('countries.json');
    $countries = json_decode($jsonData, true);
    $countryIntList = range(0, count($countries) - 1);
    $capitalIntList = array();
    foreach ( $countries as $country ) {
        if ( $country['capital'] === 0 ) continue;
        $capitalIntList[] = $country['pk'];
    }
    $questionLists = array(
        'countryIntList' => $countryIntList,
        'capitalIntList' => $capitalIntList
    );
    return array($countryIntList, $capitalIntList);
}

