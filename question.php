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
        $question['text'] = 'Name the <b>country</b> of this flag';
        $_SESSION['answer'] = $question['country'];
        if ($question['hint'] && $question['hint'][0] === "F") {
            $question['hint'] = substr($question['hint'], 2);
        } else {
            unset($question['hint']);
        }
        unset($question['country']);
        unset($question['capital']);
        break;
    case 'flagCapital':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = "Name the <b>capital</b> of this flag";
        $_SESSION['answer'] = $question['capital'];
        unset($question['hint']);
        unset($question['capital']);
        unset($question['country']);
        break;
    case 'countryCapital':
        $question['text'] = "What's the capital of 
            <b>".$question['country']."</b>?";
        $_SESSION['answer'] = $question['capital'];
        unset($question['hint']);
        unset($question['capital']);
        break;
    case 'capitalCountry':
        $question['text'] = '<b>'.$question['capital']."</b> is the capital 
            of which country?";
        $_SESSION['answer'] = $question['country'];
        if ($question['hint'] && $question['hint'][0] === "C") {
            $question['hint'] = substr($question['hint'], 2);
        } else {
            unset($question['hint']);
        }
        unset($question['country']);
        break;
}
unset($question['code']);
echo(json_encode($question, JSON_PRETTY_PRINT));