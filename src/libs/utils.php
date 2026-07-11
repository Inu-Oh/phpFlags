<?php

// Check answer accuracy and store result in session
function checkAnswerAccuracy($perc_accuracy) {
    if ( $perc_accuracy > 85 ) {
        $_SESSION['correct'] = TRUE;    
        $_SESSION['score']++;
        // TODO - Separate misspell check from the rest of this logic
        if ( $perc_accuracy < 100 ) {
                $_SESSION['misspelled'] = TRUE;
        }
    } else {
        $_SESSION['correct'] = FALSE;
    }
}

// Check the user answer for a match and return accuracy
function checkUserAnswer() {
    if ( isset($_POST['answer']) && strlen($_POST['answer']) > 0 ) {
            $_SESSION['userInput'] = htmlspecialchars($_POST['answer'], ENT_QUOTES, 'UTF-8');
            $matching_chars = similar_text(
                iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($_SESSION['userInput'])),
                iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($_SESSION['answer'])),
                $perc_accuracy );
        }
    return $perc_accuracy;
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
    if ( count($_SESSION['capitalCountry']) > 0 ) { 
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


function is_get_request(): bool {
    return strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'GET';
}


function is_post_request(): bool {
    return strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST';
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
                .htmlspecialchars($_SESSION['count'], ENT_QUOTES, 'UTF-8');
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

// Update anonymous progress data in case user creates an account or logs in
function updateAnonProgress($quizId) {
    if ( ! isset($_SESSION['sessProgress']) ) {
        $_SESSION['sessProgress'] = [];
    }
    $questionProgress = [
        $quizId,
        $_SESSION['nextQuestion'],
        $_SESSION['correct']
    ];
    $_SESSION['sessProgress'][] = $questionProgress;
}

// Update the logged in user's progress in the PostgreSQL database
function updateUserProgressInDB($pdo, $quizId) {
    if ( $_SESSION['correct'] ) {
        $sql = 'UPDATE progress 
            SET test_count=test_count+1, correct_count=correct_count+1
            WHERE user_id=:ui AND country_id=:ci AND quiz_id = :qi';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':ui' => $_SESSION['userId'],
            ':ci' => $_SESSION['nextQuestion'],
            ':qi' => $quizId
        ));
    } else {
        $sql = 'UPDATE progress 
            SET test_count=test_count+1
            WHERE user_id=:ui AND country_id=:ci AND quiz_id=:qi';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':ui' => $_SESSION['userId'],
            ':ci' => $_SESSION['nextQuestion'],
            ':qi' => $quizId
        ));
    }
}

// Make a session copy of logged in user progress from database to track in session
function updateUserProgressInSession($pdo, $quizId) {
    if ( ! isset($_SESSION['userProgress'] )) {
        $_SESSION['userProgress'] = [];
        $sql = 'SELECT quiz_id, country_id, test_count, correct_count
                    FROM progress WHERE user_id=:ui';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':ui' => $_SESSION['userId']));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ( $rows as $row ) {
            $key = strval($row['quiz_id']) . "_" . strval($row['country_id']);
            $val = array($row['test_count'], $row['correct_count']);
            $_SESSION['userProgress'][$key] = $val;
        }
    } else {
        $key = strval($quizId ). "_" . strval($_SESSION['nextQuestion']);
        list($testCount, $correctCount) = $_SESSION['userProgress'][$key];
        $testCount++;
        if ( $_SESSION['correct'] ) {
            $correctCount++;
        }
        $val = array($testCount, $correctCount);
        $_SESSION['userProgress'][$key] = $val;
    }
}

// Provide array of quiz types with the standard id for each
function quizArray() {
    return array(
        'flagCountry' => 1,
        'flagCapital' => 2,
        'countryCapital' => 3,
        'capitalCountry' => 4
    );
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

// Loads code from PHP a file and passes data to it
function view(string $filename, array $data = []): void {
    // Create variables from the associative array $data
    foreach ( $data as $key => $value ) {
        $$key = $value;
    }
    require_once __DIR__ . '/../inc/' . $filename . '.php';
}