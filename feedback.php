<?php
session_start();
require_once __DIR__ . '/src/libs/utils.php';

if ( empty($_SESSION['csrf_token']) ) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// For testing TODO remove later along with button in view below
if ( isset($_POST['clear']) || ! isset($_SESSION['quizIsSet']) ) {
    session_unset();
    session_regenerate_id(true);
    header( 'Location: index.php' );
    return;
}

if ( isset($_POST['next']) || ! isset($_SESSION['nextQuestion'])) {
    if ( ! isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }
    getQuestion();
    header( 'Location: index.php' );
    return;
}

# TODO - need to set up user progress data before this can be used
// if ( isset($_SESSION['userName'])) {
//     $stmt = $pdo->prepare('SELECT quiz_id FROM quiz WHERE quiz_name = :qn ');
//     $stmt->execute(array(':qn' => $_SESSION['currentQuiz']));
//     $row = $stmt->fetch(PDO::FETCH_ASSOC);
//     $quizId = $row['quiz_id'];
//     if ( $_SESSION['correct'] ) {
//         $sql = 'UPDATE progress 
//             SET test_count=test_count+1, correct_count=correct_count+1
//             WHERE user_id=:ui AND country_id=:ci AND quiz_id = :qi';
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute(array(
//             ':ui' => $_SESSION['userId'],
//             ':ci' => $_SESSION['nextQuestion'],
//             ':qi' => $quizId
//         ));
//     } else {
//         $sql = 'UPDATE progress 
//             SET test_count=test_count+1
//             WHERE user_id=:ui AND country_id=:ci AND quiz_id = :qi';
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute(array(
//             ':ui' => $_SESSION['userId'],
//             ':ci' => $_SESSION['nextQuestion'],
//             ':qi' => $quizId
//         ));
//     }
// }

view('head'); ?>

<?php if (isset($_SESSION['username'])) echo 'Logged in as ' . $_SESSION['username'] ?>

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