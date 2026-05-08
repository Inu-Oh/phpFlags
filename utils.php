<?php

function clearSession() {
    session_unset();
}

// Get the next quiz question and save it to session
function getQuestion() {
    $quizzes = array();
    if ( count($_SESSION['flagCountry']) > 0 ) { 
        $quizzes[] = 'flagCountry';
    }
    if ( count($_SESSION['flagCapital']) > 0 ) { 
        $quizzes[] = 'flagCapital';
    }
    if ( count($_SESSION['countryCapital']) > 0 ) { 
        $quizzes[] = 'countryCapital';
    }
    if ( count($_SESSION['flagCountry']) > 0 ) { 
        $quizzes[] = 'capitalCountry';
    }
    $randomQuiz = $quizzes[array_rand($quizzes)];
    $_SESSION['currentQuiz'] = $randomQuiz;
    switch ($randomQuiz) {
        case 'flagCountry':
            $_SESSION['nextQuestion'] = array_pop($_SESSION['flagCountry']);
            break;
        case 'flagCapital':
            $_SESSION['nextQuestion'] = array_pop($_SESSION['flagCapital']);
            break;
        case 'countryCapital':
            $_SESSION['nextQuestion'] = array_pop($_SESSION['countryCapital']);
            break;
        case 'capitalCountry':
            $_SESSION['nextQuestion'] = array_pop($_SESSION['capitalCountry']);
            break;
    }
    if ( ! isset($_SESSION['nextQuestion']) ) {
        getQuestion;
    }
    // TODO - remove vardumps after testing
    var_dump($_SESSION['nextQuestion']);
    var_dump(count($_SESSION['flagCountry']), count($_SESSION['flagCapital']),
        count($_SESSION['countryCapital']), count($_SESSION['capitalCountry']));
}

// Set up all quiz questions to session at start or restart
function setQuestions() {
    list($countryIntList, $capitalIntList) = quizLists();

    // var_dump($countryIntList);
    shuffle($countryIntList);
    $_SESSION['flagCountry'] = $countryIntList;
    shuffle($capitalIntList);
    $_SESSION['flagCapital'] = $capitalIntList;
    shuffle($capitalIntList);
    $_SESSION['countryCapital'] = $capitalIntList;
    shuffle($capitalIntList);
    $_SESSION['capitalCountry'] = $capitalIntList;
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
