<?php
session_start();
require_once 'utils.php';

// For testing TODO remove later along with button in view below
if ( isset($_POST['clear']) || ! isset($_SESSION['quizIsSet']) ) {
    session_unset();
    header( 'Location: index.php' );
    return;
}

if ( isset($_POST['next']) || ! isset($_SESSION['nextQuestion'])) {
    getQuestion();
    $_SESSION['loaded'] = TRUE;
    $_SESSION['feedback'] = FALSE;
    header( 'Location: index.php' );
    return;
}

require_once 'head.php'; ?>

<div id="q-card" class="container pt-3 bg-light rounded-4">
    <?= scoreBoard(); ?>

    <div id="quiz-area"></div>
</div>

<script id="quiz-feedback" type="text/x-handlebars-template">
<div class="px-3">

    <div id="feedback" class="text-center">
        <h2 class="pb-2">
            {{#if feedback.correct }}
                You got it right
            {{else}}
                Better luck next time
            {{/if}}
        </h2>
        
        {{#if feedback.misspelled }}
            <p class="text-small">...but check your spelling</p>
        {{/if}}

        {{#if feedback.src}}
            <img id="f-img" src="{{ feedback.src }}" alt="" class="rounded-1">
        {{/if}}

        <h3 class="pt-3">{{{ feedback.text }}}</h3></span>
    </div>

    <div id="form-div">

    <form method="post" action="" class="form-group pt-5">
        <div id="q-form" class="row">
            <div class="col-9 text-center">
                <input id="answer" type="text" name="answer" class="form-control"
                    value="{{ feedback.user_input }}" disabled>
            </div>
            <div class="col-3">
                <input id="check-button" type="submit" 
                    class="btn btn-outline-success form-control" 
                    name="next" value="Next" autofocus>
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
    $.getJSON('answer.php', function(feedback) {
        window.console && console.log(feedback);
        var source = $('#quiz-feedback').html();
        var template = Handlebars.compile(source);
        var context = {};
        context.feedback = feedback;
        $('#quiz-area').replaceWith(template(context));
    }).fail( function() { alert('getJSON feedback fail'); } );
});
</script>

</body>
</html>