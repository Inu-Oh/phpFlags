<?php
session_start();
header("Content-type: application/json; charset=utf-8");
require_once __DIR__ . '/src/pdo.php';

// Prapare array of question data to be sent via JSON to Handlebars template
// only if user has entered an answer and feedbck has been set to true
if ( isset($_SESSION['feedback']) ) {
    if ($_SESSION['feedback'] === TRUE ) {
        $stmt = $pdo->prepare('SELECT * FROM Countries WHERE pk = :pk');
        $stmt->execute(array(':pk' => $_SESSION['nextQuestion']));
        $feedback = $stmt->fetch(PDO::FETCH_ASSOC);
        // Add feedback data from session
        $feedback['correct'] = $_SESSION['correct'];
        $feedback['user_input'] = $_SESSION['userInput'];
        $feedback['answer'] = $_SESSION['answer'];
        if ( isset($_SESSION['misspelled']) && $_SESSION['misspelled'] === TRUE ) {
            $feedback['misspelled'] = TRUE;
        }
        // Conform data depnding on quiz type
        switch ( $_SESSION['currentQuiz'] ) {
            case 'flagCountry':
                $feedback['src'] = 'static/images/'.$feedback['code'].'.png';
                $feedback['text'] = 'This is the flag of '.$feedback['country'];
                break;
            case 'flagCapital':
                $feedback['src'] = 'static/images/'.$feedback['code'].'.png';
                $feedback['text'] = 'This flag belongs to '.$feedback['country'].
                    ' whose capital is '.$feedback['capital'];
                break;
            case 'countryCapital':
                $feedback['text'] = 'The capital of '.$feedback['country'].
                    ' is '.$feedback['capital'];
                break;
            case 'capitalCountry':
                $feedback['text'] = $feedback['capital'].' is the capital of '
                    .$feedback['country'];
                break;
        }
        unset($feedback['country'],$feedback['capital'], $feedback['code'],
            $feedback['hint'], $feedback['pk'], $_SESSION['misspelled']);
        echo(json_encode($feedback, JSON_PRETTY_PRINT));
    }
}