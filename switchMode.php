<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) ) $_SESSION['csrf_token'] = bin2hex( random_bytes(32) );

if ( isPostRequest() ) {

    verifyCsrfOrDie();

    // Wipe previous quiz mode from session and write quiz list for newsly selected mode
    if ( isset( $_SESSION['currentQuiz'] ) ) unset( $_SESSION['currentQuiz'] );
    if ( isset( $_SESSION['nextQuestion'] ) ) unset( $_SESSION['nextQuestion'] );

    if ( isset( $_POST['learn'] ) ) {

        if ( isset( $_SESSION['practiceList'] ) ) unset( $_SESSION['practiceList'] );
        if ( isset( $_SESSION['reviewList'] ) ) unset( $_SESSION['reviewList']  );
        if ( isset( $_SESSION['quizMode'] ) ) unset( $_SESSION['quizMode'] );

    } elseif ( isset( $_POST['practice'] ) ) {

        if ( isset( $_SESSION['practiceList'] ) ) unset( $_SESSION['practiceList'] );
        if ( isset( $_SESSION['reviewList'] ) ) unset( $_SESSION['reviewList']  );

        $_SESSION['quizMode'] = 'practice';
        getUserPracticeList();
        setModeQuizStats();
        
    } elseif ( isset( $_POST['review']) || 
        ( isset( $_GET['mode'] ) && $_GET['mode'] == 'review' ) ) {

        if ( isset( $_SESSION['practiceList'] ) ) unset( $_SESSION['practiceList'] );
        if ( isset( $_SESSION['reviewList'] ) ) unset( $_SESSION['reviewList']  );

        $_SESSION['quizMode'] = 'review';
        getUserReviewList();
        setModeQuizStats();
    }
}

header( 'Location: index.php' );
return;