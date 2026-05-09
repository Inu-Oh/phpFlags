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

            $_SESSION['score'] += 5;
        }
        $_SESSION['loaded'] = FALSE;
        header( 'Location: index.php' );
        return;
    }
}

if ( $_SESSION['loaded'] === FALSE ) {

    getQuestion();
    $_SESSION['loaded'] = TRUE;
    // var_dump($_SESSION['nextQuestion']);
}

require_once 'head.php';
?>
<div class="container pt-5 w-50">
    <div>
        <h2 id="score">Score : <?= $_SESSION['score'] ?></h2>
    </div>

    <div id="quiz-area"></div>
<!-- <form method="post" class="pt-4">
    <input class="btn btn-outline-danger me-3" type="submit"
        value="Clear session" name="clear">
</form> -->
</div>

<script id="quiz-template" type="text/x-handlebars-template">
<div >
    <h1>{{ question.text }}</h1>
    <div>
    {{#if question.country}}<h3>{{ question.country }}</h3>{{else}}{{/if}}
    {{#if question.capital}}<h5>{{ question.capital }}</h5>{{else}}{{/if}}
    {{#if question.hint}}<h5>{{ question.hint }}</h5>{{else}}{{/if}}
    {{#if question.src}}
        <img src="{{ question.src }}" alt="" class="w-100">
    {{else}}{{/if}}
    </div>
    <div id="form-div">
        <form method="post" action="" class="form-group row py-5">
            <div class="col-9">
                <input id="answer" type="text" name="answer" class="form-control"
                    placeholder="..." autofocus >
            </div>
            <div class="col-3">
                <input class="btn btn-outline-success form-control" type="submit" 
                name="check" value="Check" autocomplete="off">
            </div>
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