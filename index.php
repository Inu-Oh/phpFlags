<?php
session_start();
require_once 'utils.php';

if ( empty($_SESSION['csrf_token']) ) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// For testing TODO remove later along with button in view below
if ( isset($_POST['clear']) ) {
    session_unset();
    session_regenerate_id(true);
    header( 'Location: index.php' );
    return;
}

if ( ! isset($_SESSION['quizIsSet']) ) {

    setQuestions();
    // Start new score session
    $_SESSION['count'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['feedback'] = FALSE;
    getQuestion();
}

if ( ! isset($_SESSION['nextQuestion']) ) {
    getQuestion();
}

if ( isset($_POST['check'])) {
    if ( ! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }
    if ( isset($_POST['answer']) && strlen($_POST['answer']) > 0 ) {
        $_SESSION['userInput'] = htmlspecialchars($_POST['answer'], ENT_QUOTES, 'UTF-8');
        $matching_chars = similar_text(
            iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($_SESSION['userInput'])),
            iconv('UTF-8', 'ASCII//TRANSLIT', strtolower($_SESSION['answer'])),
            $perc_accuracy );
        if ( $perc_accuracy > 85 ) {
            $_SESSION['correct'] = TRUE;    
            $_SESSION['score']++;
            if ( $perc_accuracy < 100 ) {
                $_SESSION['misspelled'] = TRUE;
            }
        } else {
            $_SESSION['correct'] = FALSE;
        }
        $_SESSION['count']++;
        $_SESSION['feedback'] = TRUE;
        header( 'Location: feedback.php' );
        return;
    }
} 

require_once 'head.php'; ?>

<div id="q-card" class="container pt-3 bg-light rounded-4">
    <?= scoreBoard(); ?>

    <div id="quiz-area"></div>
</div>

<script id="quiz-template" type="text/x-handlebars-template">
<div class="px-3">

    {{#if question.quiz }}
    <div class="text-center">
        <h2 class="pb-2 fw-bold">{{ question.quiz }}</h2>
    </div>
    {{/if}}

    <div id="question" class="text-center">
        {{#if question.country}}
            <h3 class="pb-2">{{ question.text }}</h3>
        {{/if}}

        {{#if question.capital}}
            <h3 class="pb-2">{{ question.text }}</h3>
            {{#if question.hint}}
                <h5>Hint: {{ question.hint }}</h5>
            {{/if}}
        {{/if}}

        {{#if question.src}}
            <h3 class="pb-2">{{ question.text }}</h3>
            {{#if question.hint}}
                <h5>Hint: {{ question.hint }}</h5>
            {{/if}}
            <img id="q-img" src="{{ question.src }}" alt="" class="rounded-1">
        {{/if}}
    </div>

    <div id="form-div">

    <form method="post" action="" class="form-group pt-5">
        <div id="q-form" class="row">
            <input type="hidden" name="csrf_token"
                value="<?= $_SESSION['csrf_token'] ?>">
            <div class="col-9">
                <input id="answer" type="text" name="answer" class="form-control"
                    placeholder="{{ question.placeholder }}" autofocus
                    autocomplete="off">
            </div>
            <div class="col-3">
                <input id="check-button" type="submit" 
                    class="btn btn-outline-success form-control" 
                    name="check" value="Check">
            </div>
        </div>

        <!-- <input class="btn btn-outline-danger me-3" type="submit"
            value="Clear session" name="clear"> -->
    </form>

    </div>
</div>
</script>

<script>
$(document).ready(function() {
    $.getJSON('question.php', function(question) {
        window.console && console.log(question);
        var source = $('#quiz-template').html();
        var template = Handlebars.compile(source);
        var context = {};
        context.question = question;
        $('#quiz-area').replaceWith(template(context));
    }).fail( function() { alert('getJSON question fail'); } );
});
</script>

</body>
</html>