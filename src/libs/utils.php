<?php
// Constants for quiz lists
define("FLAG_COUNTRY", 1);
define("FLAG_CAPITAL", 2);
define("COUNTRY_CAPITAL", 3);
define("CAPITAL_COUNTRY", 4);

// Check answer accuracy and store result in session
function checkAnswerAccuracy($percAccuracy): void {
    if ( $percAccuracy > 85 ) {
        $_SESSION['correct'] = TRUE;    
        $_SESSION['score']++;
        if ( $percAccuracy < 100 ) $_SESSION['misspelled'] = TRUE;

    } else {
        $_SESSION['correct'] = FALSE;
    }
}

// Check the user answer for a match and return accuracy
function checkUserAnswer(): float {
    if ( isset( $_POST['answer'] ) && strlen( $_POST['answer'] ) > 0 ) {
        $_SESSION['userInput'] = htmlspecialchars($_POST['answer'], ENT_QUOTES, 'UTF-8');
        $matchingChars = similar_text(
            iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($_SESSION['userInput'])),
            iconv('UTF-8', 'ASCII//TRANSLIT', strtolower(
                htmlspecialchars($_SESSION['answer']))
            ),
            $percAccuracy
        );
    }

    return $percAccuracy;
}

// Prevent showing HTML code for select few special chars in user feedback
function cleanUpUserInputForOutput(): void {
        
    if ( str_contains( $_SESSION['userInput'], '&#039;' ) ) {
        $_SESSION['userInput'] = str_replace( '&#039;', "'", $_SESSION['userInput'] );
    }
    if ( str_contains( $_SESSION['userInput'], '&#045;' ) ) {
        $_SESSION['userInput'] = str_replace( '&#045;', "-", $_SESSION['userInput'] );
    }
}

// Get question for learn quiz mode
function getLearnQuestion(): void {
    // Make an array of all quizzes to choose from
    $quizzes = array();
    if ( count($_SESSION['flagCountry']) > 0 ) $quizzes[] = 'flagCountry';
    if ( count($_SESSION['flagCapital']) > 0 ) $quizzes[] = 'flagCapital';
    if ( count($_SESSION['countryCapital']) > 0 ) $quizzes[] = 'countryCapital';
    if ( count($_SESSION['capitalCountry']) > 0 ) $quizzes[] = 'capitalCountry';
    
    // Choose a random question from a rendomly selected quiz list
    $randomQuiz = $quizzes[array_rand($quizzes)];
    if ( isset($_SESSION['nextQuestion']) ) unset($_SESSION['nextQuestion']);
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
    if ( ! isset($_SESSION['nextQuestion']) ) getLearnQuestion();

    $_SESSION['loaded'] = TRUE;
    $_SESSION['feedback'] = FALSE;
}

// Get the next quiz question and save it to session
function getQuestion(): void {

    if ( ! isset( $_SESSION['quizMode'] ) ) {

        getLearnQuestion();

    } elseif ( $_SESSION['quizMode'] == 'practice' ) {
    
        getPracticeQuestion();
    
    } else {

        getReviewQuestion();
    }
}

// Get question for practice quiz mode 
function getPracticeQuestion(): void {

    if ( count( $_SESSION['practiceList'] ) > 0 ) {

        $nextQuestion = array_shift( $_SESSION['practiceList'] );
        $quizId = $nextQuestion['quizId'];
        $questionId = $nextQuestion['questionId'];
        setQuizAndQuestion($quizId, $questionId) ;

    } else {
        // Switch to learn mode if no more practice questions
        unset( $_SESSION['practiceList'], $_SESSION['quizMode'] );

        $_SESSION['message'] = "You completed all the practice questions.
                                Resuming learning questions.";

        getQuestion();
    }
}

// Get question for review quiz mode
function getReviewQuestion(): void {

    if ( count( $_SESSION['reviewList'] ) > 0 ) {
        
        $nextQuestion = array_shift( $_SESSION['reviewList'] );
        list( $quizId, $questionId ) = $nextQuestion;
        setQuizAndQuestion($quizId, $questionId);

    } else {
        // Switch to learn mode if no more practice questions
        unset( $_SESSION['reviewList'], $_SESSION['quizMode'] );

        $_SESSION['message'] = "You completed all the review questions.
                                Resuming learning questions.";

        getQuestion();
    }
}

// Return list of questions from user's learned questions ordered from low to high grade
function getUserPracticeList(): void {

    // Create an array of practice questions with value grading each question
    $_SESSION['practiceList'] = array();

    foreach ( $_SESSION['userProgress'] as $key => $questionProgress ) {

        list( $views, $correct ) = $questionProgress;

        if ( $views > 0 ) {
            $questionAccuracy = $correct / $views;

            if ( $questionAccuracy < 0.8 ) {

                $quizId = intval( round( $key / 10000 ) );
                $questionId = $key % 10000;
                $_SESSION['practiceList'][] = array(
                    'quizId' => $quizId,
                    'questionId' => $questionId,
                    'accuracy' => $questionAccuracy
                );
            }
        }
    }
    
    # Order the array from lowest to highest accuracy score per question
    usort( $_SESSION['practiceList'], function( $a, $b ) {
        return $a['accuracy'] <=> $b['accuracy'];
    });
}

// Return random list of questions from user's learned question set
function getUserReviewList() {

    // Make a list of all previously tested questions from user progress
    $_SESSION['reviewList'] = array();

    foreach ( ($_SESSION['userProgress']) as $key => $questionProgress ) {
        list($views, $_) = $questionProgress;
        if ( $views > 0 ) {
            $quizId = intval( round( $key / 10000 ) );
            $questionId = $key % 10000;
            $_SESSION['reviewList'][] = array( $quizId, $questionId );
        }
    }

    // Order the list randomly
    shuffle( $_SESSION['reviewList'] );
}


function getUserStats($pdo, $quizId): void {

    if ( ! isset($_SESSION['userProgress']) ) updateUserProgressInSession($pdo, $quizId);

    # Calculate user's performance on all questions overall
    $total = $seen = $correct = 0;
    foreach ( $_SESSION['userProgress'] as $questionProgress ) {
        if ( $questionProgress[0] > 0 ) {
            $seen += $questionProgress[0];
            $correct += $questionProgress[1];
        }
        $total++;
    }

    $accuracy = ( $seen > 0 ) ? ( $correct / $seen ) * 100 : 0;
    
    // Store calculated performance data in session
    $_SESSION['userTested'] = $seen;
    $_SESSION['userCorrect'] = $correct;
    $_SESSION['questionCount'] = $total;
    $_SESSION['userAccuracy'] = $accuracy;
}

// Return grade based on percentage score
function grade(): string {
    if ( ( isset($_SESSION['count']) && $_SESSION['count'] > 0 ) || 
        isset($_SESSION['userAccuracy']) ) {
            
        if ( isset($_SESSION['userAccuracy'])) {
            $perc = round($_SESSION['userAccuracy']);
        } else {
            $perc = round(($_SESSION['score'] / $_SESSION['count']) * 100);
        }
        
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


function isGetRequest(): bool {
    return strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'GET';
}


function isPostRequest(): bool {
    return strtoupper( $_SERVER['REQUEST_METHOD'] ) === 'POST';
}

// Creates HTML for scoreboard
function scoreBoard($pdo, $quizId=FALSE): string {

    // Set up data to use in scoreboard depending on user or anonymous data
    if ( isset($_SESSION['username']) ) {
        if ( ! isset($_SESSION['userTested']) || ! isset($_SESSION['userAccuracy']) ) {
            getUserStats($pdo, $quizId);
        }
        $seen = $_SESSION['userTested'];
        $score = round($_SESSION['userAccuracy']) . '%';
        $conjunction = ' on ';
    } else {
        $seen = $_SESSION['count'];
        $score = $_SESSION['score'];
        $conjunction = ' out of ';
    }
    $card_s = ( $seen != 1 ) ? ' cards ' : ' card ';

    $scoreBoard = '<div class="text-center p-3">
        <h3 id="score" class="bg-secondary text-light rounded py-1">';

    // Text output for review and practice quiz modes
    if ( isset( $_SESSION['quizMode'] ) ) {
        $scoreBoard .=  '<span class="text-warning fw-bold">' . 
                            htmlspecialchars( ucwords( $_SESSION['quizMode'] ) ) . 
                        '</span>&nbsp;&nbsp; ';
        
        if ( $_SESSION['quizMode'] == 'practice' ) {
            $scoreBoard .= htmlspecialchars( count( $_SESSION['practiceList'] ) );

        } elseif ( $_SESSION['quizMode'] == 'review' ) {
            $scoreBoard .= htmlspecialchars( count( $_SESSION['reviewList'] ) );
        }
        
        $scoreBoard .= ' to go &nbsp; ' . 
            '<span class="text-warning">' .
                htmlspecialchars($score, ENT_QUOTES, 'UTF-8') .
            '</span>';

    // Text output for learning quiz mode
    } else {
        if ( $seen > 0 ) {
            if ( $score == $seen || $score == '100%' ) {
                $scoreBoard .= 'Perfect score - ' .
                    htmlspecialchars($_SESSION['count'], ENT_QUOTES, 'UTF-8') . $card_s;

            } else {
                $scoreBoard .= 'You got '
                    . htmlspecialchars($score, ENT_QUOTES, 'UTF-8') . $conjunction
                    . htmlspecialchars($_SESSION['count'], ENT_QUOTES, 'UTF-8') . $card_s;
            }
        } else {
            $scoreBoard .= 'Starting new quiz';
        }
    }
    
    // Add emoji to emphasize score level
    if ( grade() ) $scoreBoard .= ' &nbsp; ' . grade();
    $scoreBoard .= '</h3></div>';

    return $scoreBoard;
}

// Set up all quiz questions to session at start or restart
function setQuestions($pdo): void {
    list($countryIntList, $capitalIntList) = quizLists($pdo);

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

// Helper function for getting review and practice questions
function setQuizAndQuestion($quizId, $questionId) {
    switch ( intval($quizId) ) {
        case 1 :
            $_SESSION['currentQuiz'] = 'flagCountry';
            break;
        case 2:
            $_SESSION['currentQuiz'] = 'flagCapital';
            break;
        case 3:
            $_SESSION['currentQuiz'] = 'countryCapital';
            break;
        case 4:
            $_SESSION['currentQuiz'] = 'capitalCountry';
            break;
    }
    $_SESSION['nextQuestion'] = intval($questionId);
}

// Update anonymous progress after each test in case user creates an account or logs in
function updateAnonProgress($quizId): void {
    if ( ! isset($_SESSION['anonProgress']) ) {
        $_SESSION['anonProgress'] = [];
    }
    $questionProgress = [
        $quizId,
        $_SESSION['nextQuestion'],
        $_SESSION['correct']
    ];
    $_SESSION['anonProgress'][] = $questionProgress;
}


function updateScore($pdo, $quizId, $percAccuracy): void {

    if ( isset($_SESSION['username'])) {

        if ( ! isset($_SESSION['userTested']) || ! isset($_SESSION['userAccuracy']) ||
            ! isset($_SESSION['questionCount']) || ! isset($_SESSION['userCorrect']) ) {

            getUserStats($pdo, $quizId);
        }

        $_SESSION['userTested']++;
        if ( $percAccuracy > 85 ) $_SESSION['userCorrect']++;

        $_SESSION['userAccuracy'] = ($_SESSION['userCorrect'] / $_SESSION['userTested']) * 100;
    }
    // Increment count only when quiz is in learn mode
    if ( ! isset($_SESSION['quizMode']) ) $_SESSION['count']++;
}

// Update the logged in user's progress in the PostgreSQL database
function updateUserProgressInDB($pdo, $quizId): void {
    if ( $_SESSION['correct'] ) {
        $sql = 'UPDATE progress 
            SET test_count=test_count+1, correct_count=correct_count+1,
            updated_at = NOW()
            WHERE user_id=:ui AND country_id=:ci AND quiz_id = :qi';
        
    } else {
        $sql = 'UPDATE progress 
            SET test_count=test_count+1, updated_at = NOW()
            WHERE user_id=:ui AND country_id=:ci AND quiz_id=:qi';
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':ui' => $_SESSION['userId'],
        ':ci' => $_SESSION['nextQuestion'],
        ':qi' => $quizId
    ));
}

// Make a session copy of logged in user progress from database to track in session
function updateUserProgressInSession($pdo, $quizId): void {
    if ( ! isset($_SESSION['userProgress'] )) {

        $_SESSION['userProgress'] = [];
        $sql = 'SELECT quiz_id, country_id, test_count, correct_count
                    FROM progress WHERE user_id=:ui';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':ui' => $_SESSION['userId']));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $totalTested = 0;
        foreach ( $rows as $row ) {
            if ( $row['test_count'] > 0 ) {
                $totalTested++;
            }
            $key = $row['quiz_id'] * 10000 + $row['country_id'];
            $val = array($row['test_count'], $row['correct_count']);
            $_SESSION['userProgress'][$key] = $val;
        }
        $_SESSION['count'] = $totalTested;

    } elseif ( $quizId ) {

        $key = $quizId * 10000 + $_SESSION['nextQuestion'];
        list($testCount, $correctCount) = $_SESSION['userProgress'][$key];
        $testCount++;
        if ( $_SESSION['correct'] ) $correctCount++; # TODO REview Does this duplicate update score?
        $val = array($testCount, $correctCount);
        $_SESSION['userProgress'][$key] = $val;
    }
}

// At login / signup update user progress in Postress DB from anonymous session data
function updateUserProgressFromSessionToDB($pdo): void {
    if ( isset($_SESSION['anonProgress']) ) {
        foreach ( $_SESSION['anonProgress'] as $questionProgress)  {
            list($quizId, $countryId, $correct) = $questionProgress;
            $primaryKey = array(
                ':ui' => $_SESSION['userId'],
                ':ci' => $countryId,
                ':qi' => $quizId
            );
            if ( $correct ) {
                $sql = 'UPDATE progress 
                            SET test_count=1, correct_count=1, updated_at = NOW()
                            WHERE user_id=:ui AND country_id=:ci AND quiz_id=:qi';
            } else {
                $sql = 'UPDATE progress
                            SET test_count=1, updated_at = NOW()
                            WHERE user_id=:ui AND country_id=:ci AND quiz_id=:qi';
            }
            $stmt = $pdo->prepare($sql);
            $stmt->execute($primaryKey);
        }
        unset($_SESSION['anonProgress']);
    }
}

// Provide array of quiz types with a constant id used in DB for each
function quizArray(): array {
    return array(
        'flagCountry' => FLAG_COUNTRY,
        'flagCapital' => FLAG_CAPITAL,
        'countryCapital' => COUNTRY_CAPITAL,
        'capitalCountry' => CAPITAL_COUNTRY
    );
}

// Create lists of integers for use as quiz lists
function quizLists($pdo): array {
    $stmt = $pdo->prepare('SELECT pk, capital FROM Countries');
    $stmt->execute(array());
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $count = 0;
    $countries = array();
    if ( ! empty($rows) ) {
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

function verifyCsrfOrDie() {
    if ( ! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }
}

// Loads code from PHP file and passes data to it
function view(string $filename, array $data = []): void {
    // Create variables from the associative array $data
    foreach ( $data as $key => $value ) $$key = $value;

    require_once __DIR__ . '/../inc/' . $filename . '.php';
}