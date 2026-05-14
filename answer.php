<?php
session_start();
header("Content-type: application/json; charset=utf-8");

// Prapare array of question data to be sent via JSON to Handlebars template
$jsonData = file_get_contents('countries.json');
$countryList = json_decode($jsonData, true);
$currentQuestion = $_SESSION['nextQuestion'];
$feedback = $countryList[$currentQuestion];
if ( isset($_SESSION['feedback']) ) {
    if ($_SESSION['feedback'] === TRUE ) {
        $feedback['correct'] = $_SESSION['correct'];
        $feedback['user_input'] = $_SESSION['userInput'];
        $feedback['answer'] = $_SESSION['answer'];
        if ( isset($_SESSION['misspelled']) && $_SESSION['misspelled'] === TRUE ) {
            $feedback['misspelled'] = TRUE;
        }
        switch ( $_SESSION['currentQuiz'] ) {
            case 'flagCountry':
                $feedback['src'] = 'static/images/'.$feedback['code'].'.png';
                $feedback['text'] = 'This is the flag of <strong>'
                    .$feedback['country'].'</strong>';
                break;
            case 'flagCapital':
                $feedback['src'] = 'static/images/'.$feedback['code'].'.png';
                $feedback['text'] = 'This flag belongs to '.$feedback['country'].
                    ' whose capital is <strong>'.$feedback['capital'].'</strong>';
                break;
            case 'countryCapital':
                $feedback['text'] = 'The capital of '.$feedback['country'].
                    ' is <strong>'.$feedback['capital'].'</strong>';
                break;
            case 'capitalCountry':
                $feedback['text'] = $feedback['capital'].' is the capital of <strong>'
                    .$feedback['country'].'</strong>';
                break;
        }
        unset($feedback['country'],$feedback['capital'], $feedback['code'],
            $feedback['hint'], $feedback['pk'], $_SESSION['misspelled']);
        echo(json_encode($feedback, JSON_PRETTY_PRINT));
    }
}