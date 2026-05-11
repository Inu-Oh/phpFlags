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
    $_SESSION['feedback'] = FALSE;
}

if ( isset($_POST['check'])) {
    if ( isset($_POST['answer']) && strlen($_POST['answer']) > 0 ) {
        if ( strtolower(htmlentities($_POST['answer']))
            == strtolower($_SESSION['answer']) ) {
            $_SESSION['correct'] = TRUE;    
            $_SESSION['score'] += 3;
        } else {
            $_SESSION['correct'] = FALSE;
        }
        $_SESSION['user_input'] = htmlentities($_POST['answer']);
        $_SESSION['feedback'] = TRUE;
        header( 'Location: index.php' );
        return;
    }
} 

if ( isset($_POST['next'])) {
    getQuestion();
    $_SESSION['loaded'] = TRUE;
    $_SESSION['feedback'] = FALSE;
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

<?php if ( $_SESSION['feedback'] === FALSE ) {
echo '<script id="quiz-template" type="text/x-handlebars-template">
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
</script>';
} else {
echo '<script id="quiz-feedback" type="text/x-handlebars-template">
<div class="px-3">

    <div id="feedback" class="text-center">
        <h3>{{ feedback.answer }}</h3></span>
    </div>

    <div id="form-div">

    <form method="post" action="" class="form-group pt-5">
        <div id="q-form" class="row">
            <div class="col-9 text-center">
                {{#if feedback.correct }}
                    <span class="text-success fw-bold">
                {{else}}
                    <span class="text-danger fw-bold">
                {{/if}} 
                <h4>{{ feedback.user_input }}</h4></span>
            </div>
            <div class="col-3">
                <input id="check-button" type="submit" 
                    class="btn btn-outline-success form-control" 
                    name="next" value="Next">
            </div>
        </div>

        <!-- <input class="btn btn-outline-danger me-3" type="submit"
            value="Clear session" name="clear"> -->
    </form>

    </div>
</div>
</script>';
}
?>


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

    $.getJSON('feedback.php', function(feedback) {
        window.console && console.log(feedback);
        var source = $('#quiz-feedback').html();
        var template = Handlebars.compile(source);
        var context = {};
        context.feedback = feedback;
        $('#quiz-area').replaceWith(template(context));
    }).fail( function() { alert('getJSON fail'); } );
});
</script>

</body>
</html>