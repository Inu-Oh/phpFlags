<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) )
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isPostRequest() ) {

    verifyCsrfOrDie();
    header( 'Location: index.php' );
    return;
}

if ( isGetRequest() && isset( $_GET['mode'] ) ) {

    // Wipe previous quiz mode from session and write quiz list for newsly selected mode
    if ( isset( $_SESSION['currentQuiz'] ) ) unset( $_SESSION['currentQuiz'] );
    if ( isset( $_SESSION['nextQuestion'] ) ) unset( $_SESSION['nextQuestion'] );
    if ( isset( $_SESSION['challengeList'] ) )
        unset( $_SESSION['challengeList'] );
    if ( isset( $_SESSION['practiceList'] ) ) unset( $_SESSION['practiceList'] );
    if ( isset( $_SESSION['reviewList'] ) ) unset( $_SESSION['reviewList']  );

    $mode = $_GET['mode'];
    if ( $mode == 'learn' ) {

        if ( isset( $_SESSION['quizMode'] ) ) unset( $_SESSION['quizMode'] );

    } elseif ( in_array($mode, ['practice', 'review', 'challenge']) ) {

        $_SESSION['quizMode'] = $mode;
        switch ( $mode ) {
            case 'practice':
                getPracticeList(); // TODO - get rid of User in various places
                break;
            case 'review':
                getReviewList();
                break;
            case 'challenge':
                getChallengeList();
                break;
            default:
                print('error'); // TODO - improve this error
                break;
        }
        setModeQuizStats();
    } 
}

header( 'Location: index.php' );
return;