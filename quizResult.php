<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) ) 
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isPostRequest() ) {
    // Add code to wipe quiz mode data then redirect to index when user clicks submit
}

if ( isGetRequest() ) {

    if ( ! isset( $_SESSION['quizResults'] ) ) {
        header( 'Location: index.php' );
        return;
    }
}

// Add card with data to presents detailed results of practice quiz or completion of
// all cards learned