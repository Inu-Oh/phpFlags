<?php
session_start();
require_once 'utils.php';

if ( isset($_POST['clear']) ) {
    session_unset();
    header( 'Location: index.php' );
    return;
}

if ( ! isset($_SESSION['quizIsSet']) ) {

    setQuestions();
    // Start new score session
    $_SESSION['score'] = 0;
    $_SESSION['loaded'] = FALSE;
    
} else if ( isset($_POST['check'])) {
    if ( isset($_POST['answer']) && strlen($_POST['answer']) > 0 ) {
        if ( $_POST['answer'] == $_SESSION['answer'] ) {

            $_SESSION['score'] += 3;
        }
        $_SESSION['loaded'] = FALSE;
    }
}

if ( $_SESSION['loaded'] === FALSE ) {

    getQuestion();
    $_SESSION['loaded'] = TRUE;
    // var_dump($_SESSION['nextQuestion']);
}

require_once 'head.php';
?>
<div id="q-card" class="container pt-3 bg-light rounded-4">
    <div class="text-center p-3">
        <h1 id="score" class="bg-secondary fw-bold text-light rounded">
            Score : <?= $_SESSION['score'] ?></h1>
    </div>

    <div id="quiz-area"></div>
</div>

<script id="quiz-template" type="text/x-handlebars-template">
<div class="px-3">

    <div id="question" class="text-center">
    {{#if question.country}}
        <h3 class="pb-2">{{{ question.text }}}</h3>
    {{/if}}
    {{#if question.capital}}
        <h3 class="pb-2">{{{ question.text }}}</h3>
        {{#if question.hint}}
            <h5>Hint: {{ question.hint }}</h5>
        {{/if}}
    {{/if}}
    {{#if question.src}}
        <h3 class="pb-2">{{{ question.text }}}</h3>
        {{#if question.hint}}
            <h5>Hint: {{ question.hint }}</h5>
        {{/if}}
        <img id="q-img" src="{{ question.src }}" alt="" class="rounded-1">
    {{/if}}
    </div>

    <div id="feedback"></div>

    <div id="form-div">

    <form method="post" action="" class="form-group pt-5">
        <div id="q-form" class="row">
            <div class="col-9">
                <input id="answer" type="text" name="answer" class="form-control"
                    placeholder="..." autofocus autocomplete="off">
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
    }).fail( function() { alert('getJSON fail'); } );
});
</script>

</body>
</html>