<?php
session_start();
header("Content-type: application/json; charset=utf-8");

$jsonData = file_get_contents('countries.json');
$countryList = json_decode($jsonData, true);
$currentQuestion = $_SESSION['nextQuestion'];
$question = $countryList[$currentQuestion];
switch ( $_SESSION['currentQuiz'] ) {
    case 'flagCountry':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = 'Name the country';
        $_SESSION['answer'] = $question['country'];
        unset($question['country']);
        unset($question['capital']);
        break;
    case 'flagCapital':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = "Name the capital";
        $_SESSION['answer'] = $question['capital'];
        unset($question['capital']);
        unset($question['country']);
        break;
    case 'countryCapital':
        $question['text'] = 'Name the capital';
        $_SESSION['answer'] = $question['capital'];
        unset($question['capital']);
        break;
    case 'capitalCountry':
        $question['text'] = 'Name the country';
        $_SESSION['answer'] = $question['country'];
        unset($question['country']);
        break;
}
unset($question['code']);
echo(json_encode($question, JSON_PRETTY_PRINT));