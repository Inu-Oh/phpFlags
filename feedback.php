<?php
session_start();
header("Content-type: application/json; charset=utf-8");

if ( isset($_SESSION['feedback']) ) {
    if ($_SESSION['feedback'] === TRUE ) {
        $answer = array(
            'correct' => $_SESSION['correct'],
            'user_input' => $_SESSION['user_input'],
            'answer' => $_SESSION['answer']
        );
        echo(json_encode($answer, JSON_PRETTY_PRINT));
    }
}