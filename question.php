<?php
session_start();
header("Content-type: application/json; charset=utf-8");

// Prapare array of question data to be sent via JSON to Handlebars template
$jsonData = file_get_contents('countries.json');
$countryList = json_decode($jsonData, true);
$currentQuestion = $_SESSION['nextQuestion'];
$question = $countryList[$currentQuestion];
// Sanitize data for use in HTML template
foreach ( $question as $key => $value ) {
    $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
// Conform data depnding on quiz type
switch ( $_SESSION['currentQuiz'] ) {
    case 'flagCountry':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = 'Name the country of this flag';
        $_SESSION['answer'] = $question['country'];
        if ($question['hint'] && $question['hint'][0] === "F") {
            $question['hint'] = substr($question['hint'], 2);
        } else {
            unset($question['hint']);
        }
        unset($question['country'], $question['capital']);
        break;
    case 'flagCapital':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = 'Name the capital of this flag';
        $_SESSION['answer'] = $question['capital'];
        unset($question['hint'], $question['country'], $question['capital']);
        break;
    case 'countryCapital':
        $question['text'] = 'What\'s the capital of '.$question['country'].'?';
        $_SESSION['answer'] = $question['capital'];
        $question['quiz'] = 'Capital quiz';
        unset($question['hint'], $question['capital']);
        break;
    case 'capitalCountry':
        $question['text'] = $question['capital'].' is the capital of which country?';
        $_SESSION['answer'] = $question['country'];
        if ($question['hint'] && $question['hint'][0] === "C") {
            $question['hint'] = substr($question['hint'], 2);
        } else {
            unset($question['hint']);
        }
        $question['quiz'] = 'Country quiz';
        unset($question['country']);
        break;
}
unset($question['code']);
echo(json_encode($question, JSON_PRETTY_PRINT));