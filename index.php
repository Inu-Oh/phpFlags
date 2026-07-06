<?php
session_start();
require_once __DIR__ . '/src/libs/utils.php';
require_once __DIR__ . '/src/pdo.php';

if ( empty($_SESSION['csrf_token']) ) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ( ! isset($_SESSION['quizIsSet']) ) {

    setQuestions($pdo);
    // Start new score session
    $_SESSION['count'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['feedback'] = FALSE;
    getQuestion();
}

if ( ! isset($_SESSION['nextQuestion']) ) {
    getQuestion();
}

if ( is_post_request() ) {
    // For testing TODO remove later along with button in view below
    if ( isset($_POST['clear']) ) {
        session_unset();
        session_regenerate_id(true);
        header( 'Location: index.php' );
        return;
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
            // TODO - fix These are no updating correctly
            # Update user progress on question if logged in
            $quizzes = quizArray();
            $quizId = $quizzes[$_SESSION['currentQuiz']];
            if ( isset($_SESSION['username'])) {
                if ( $_SESSION['correct'] ) {
                    $sql = 'UPDATE progress 
                        SET test_count=test_count+1, correct_count=correct_count+1
                        WHERE user_id=:ui AND country_id=:ci AND quiz_id = :qi';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(
                        ':ui' => $_SESSION['userId'],
                        ':ci' => $_SESSION['nextQuestion'],
                        ':qi' => $quizId
                    ));
                } else {
                    $sql = 'UPDATE progress 
                        SET test_count=test_count+1
                        WHERE user_id=:ui AND country_id=:ci AND quiz_id=:qi';
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array(
                        ':ui' => $_SESSION['userId'],
                        ':ci' => $_SESSION['nextQuestion'],
                        ':qi' => $quizId
                    ));
                }
            } else {

                # Store progress data in case user creates an account or logs in
                if ( ! isset($_SESSION['sessProgress'])) {
                    $_SESSION['sessProgress'] = [];
                }
                $questionProgress = [
                    $quizId,
                    $_SESSION['nextQuestion'],
                    $_SESSION['correct']
                ];
                $_SESSION['sessProgress'][] = $questionProgress;
            }

            $_SESSION['count']++;
            $_SESSION['feedback'] = TRUE;
            header( 'Location: feedback.php' );
            return;
        }
    }
}

view('head'); ?>

<?php if (isset($_SESSION['username'])) echo 'Logged in as ' . $_SESSION['username'] ?>

<div id="q-card" class="container pt-3 bg-light rounded-4">
    <?= scoreBoard(); ?>

    <div id="quiz-area"></div>
    
</div>

<div>
    <?php // Temp code to work out user progress settings
    var_dump(
        $_SESSION['username'],
        $_SESSION['userId'],
        $_SESSION['currentQuiz'],
        $_SESSION['sessProgress']
        // $_SESSION['flagCountry'],
        // $_SESSION['flagCapital'], 
        // $_SESSION['countryCapital'],
        // $_SESSION['capitalCountry']
    );?>
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