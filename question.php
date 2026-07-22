<?php
require_once __DIR__ . '/src/config/config.php';
header("Content-type: application/json; charset=utf-8");
require_once __DIR__ . '/src/pdo.php';

// Prapare array of question data to be sent via JSON to Handlebars template
$stmt = $pdo->prepare('SELECT * FROM countries WHERE pk = :pk');
$stmt->execute(array(':pk' => $_SESSION['nextQuestion']));
$question = $stmt->fetch(PDO::FETCH_ASSOC);

// Conform data depnding on quiz type
switch ( $_SESSION['currentQuiz'] ) {

    case 'flagCountry':
        $question['src'] = 'static/images/'.$question['code'].'.png';
        $question['text'] = 'Name the country of this flag';
        $question['placeholder'] = 'Country name ...';
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
        $question['placeholder'] = 'Capital city name ...';
        $_SESSION['answer'] = $question['capital'];
        unset($question['hint'], $question['country'], $question['capital']);
        break;

    case 'countryCapital':
        $question['quiz'] = 'Capital quiz';
        $question['text'] = 'What\'s the capital of '.$question['country'].'?';
        $question['placeholder'] = 'Capital city name ...';
        $_SESSION['answer'] = $question['capital'];
        unset($question['hint'], $question['capital']);
        break;

    case 'capitalCountry':
        $question['quiz'] = 'Country quiz';
        $question['text'] = $question['capital'].' is the capital of which country?';
        $question['placeholder'] = 'Country name ...';
        $_SESSION['answer'] = $question['country'];
        if ($question['hint'] && $question['hint'][0] === "C") {
            $question['hint'] = substr($question['hint'], 2);
        } else {
            unset($question['hint']);
        }
        unset($question['country']);
        break;
}

unset($question['code'], $question['pk']);
echo(json_encode($question, JSON_PRETTY_PRINT));