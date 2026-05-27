<?php

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
    if ( isset($_SESSION['nextQuestion']) ) {
        unset($_SESSION['nextQuestion']);
    }
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
        getQuestion();
    }

    $_SESSION['loaded'] = TRUE;
    $_SESSION['feedback'] = FALSE;
}

// return grade based on percentage score
function grade() {
    if ($_SESSION['count'] > 0) {
        $perc = intval(($_SESSION['score'] / $_SESSION['count']) * 100);
        if ( $perc > 85 ) {
            $grade = '<i class="fa-regular fa-face-grin-stars"></i>';
        } elseif ( $perc > 70 ) {
            $grade = '<i class="fa-regular fa-face-grin-squint-tears"></i>';
        } elseif ( $perc > 55 ) {
            $grade = '<i class="fa-regular fa-face-grin-tears"></i>';
        } elseif ( $perc > 35 ) {
            $grade = '<i class="fa-regular fa-face-grin"></i>';
        } elseif ( $perc > 20 ) {
            $grade = '<i class="fa-regular fa-face-frown-open"></i>';
        } else {
            $grade = '<i class="fa-regular fa-face-sad-cry"></i>';
        }
    } else {
        $grade = "";
    }
    return $grade;
}


function scoreBoard() {
    $scoreBoard = '<div class="text-center p-3">
        <h3 id="score" class="bg-secondary text-light rounded py-1">';
    if ( $_SESSION['count'] > 0 ) {
        if ( $_SESSION['score'] == $_SESSION['count'] ) {
            $scoreBoard .= 'Perfect score on '.
                htmlspecialchars($_SESSION['count'], ENT_QUOTES, 'UTF-8').
                ' questions ';
        } else {
            $scoreBoard .= 'You got '
                .htmlspecialchars($_SESSION['score'], ENT_QUOTES, 'UTF-8').' out of '
                .htmlspecialchars($_SESSION['count'], ENT_QUOTES, 'UTF-8').' right ';
        }

    } else {
        $scoreBoard.='Starting new quiz';
    }
    if ( grade() ) {
        $scoreBoard.=' &nbsp; '.grade();
    }
    $scoreBoard.='</h3></div>';

    return $scoreBoard;
}

// Set up all quiz questions to session at start or restart
function setQuestions($pdo) {
    list($countryIntList, $capitalIntList) = quizLists($pdo);

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
function quizLists($pdo) {
    $stmt = $pdo->prepare('SELECT pk, capital FROM Countries');
    $stmt->execute(array());
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    $countries = array();
    if ( ! empty($rows ) ) {
        foreach ( $rows as $row ) {
            $count++;
            $countries[] = $row;
        }
    }

    $countryIntList = range(0, $count - 1);
    $capitalIntList = array();
    foreach ( $countries as $country ) {
        if ( $country['capital'] == 0 ) continue;
        $capitalIntList[] = $country['pk'];
    }
    $questionLists = array(
        'countryIntList' => $countryIntList,
        'capitalIntList' => $capitalIntList
    );
    return array($countryIntList, $capitalIntList);
}
