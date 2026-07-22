<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty($_SESSION['csrf_token']) ) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if ( isPostRequest() ) {

    verifyCsrfOrDie();

    // Get data for next quiz question when user clicks 'Next'
    if ( isset($_POST['next']) || ! isset($_SESSION['nextQuestion']) ) {
        
        getQuestion();
        header( 'Location: index.php' );
        return;
    }
}

if ( isGetRequest() ) {

    // Prevent opening feedback page when user clicks browser back arrow
    if ( ! isset($_SESSION['feedback']) || ! $_SESSION['feedback'] ) {
        
        header( 'Location: index.php' );
        return;
    }

    // Clear unneeded data from session
    if ( isset($_SESSION['username']) ) {

        if ( isset($_SESSION['count']) ) unset($_SESSION['count']);
        if ( isset($_SESSION['score']) ) unset($_SESSION['score']);
    }
}

view('head'); ?>

<div id="q-card" class="container pt-3 bg-light rounded-4">

    <?= scoreBoard($pdo, $_SESSION['currentQuiz']); ?>

    <div id="quiz-area"></div>

</div>
</main>

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

        <h3 class="pt-3">{{ feedback.text }}</h3></span>
    </div>

    <div id="form-div">

    <form method="post" action="" class="form-group pt-5">
        <div id="q-form" class="row">
            <input type="hidden" name="csrf_token"
                value="<?= $_SESSION['csrf_token'] ?>">
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