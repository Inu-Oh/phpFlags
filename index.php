<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty($_SESSION['csrf_token']) ) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if ( isPostRequest() ) {

    verifyCsrfOrDie();

    if ( isset($_POST['check']) ) {

        // Check the user answer and spelling accuracy
        if ( isset( $_POST['answer'] ) && strlen( $_POST['answer'] ) > 1 ) {
        $percAccuracy = checkUserAnswer(); # Returns percent value of answer
        checkAnswerAccuracy($percAccuracy); # Would return true or false

        // ...or return to question card if no input provided
        } else {
            $_SESSION['message'] = 'Write your answer in the check field';
            header( 'Location: index.php' );
            return;
        }

        // Prevent showing HTML code for hyphen and apostrophe in feedback
        cleanUpUserInputForOutput();

        // Get current quiz data
        $quizzes = quizArray();
        $quizId = $quizzes[$_SESSION['currentQuiz']];

        if ( isset($_SESSION['username']) ) {

            // Update logged in user progress in DB and session if user is logged in
            updateUserProgressInDB($pdo, $quizId);
            updateUserProgressInSession($pdo, $quizId);

        } else {

            // Store anonymous progress data in case user creates an account or logs in
            updateAnonProgress($quizId);
        }

        updateScore($pdo, $quizId, $percAccuracy);

        // These session variables are set to prevent user hitting the back arrow
        $_SESSION['feedback'] = TRUE;
        $_SESSION['loaded'] = FALSE;
        $_SESSION['testedCards']++;

        header( 'Location: feedback.php' );
        return;
    }
}

if ( isGetRequest() ) {

    if ( ! isset($_SESSION['quizIsSet']) ) {

        setQuestions($pdo);
        // Start new score session
        $_SESSION['score'] = 0;
        $_SESSION['testedCards'] = 0;
        getQuestion();
    }

    // Get question data if it is not set
    if ( ! isset($_SESSION['nextQuestion']) ) getQuestion();

    // Prevent user hitting back arrow to move from feedback back to quiz question
    if ( $_SESSION['loaded'] == FALSE ) {
        header( 'Location: feedback.php' );
        return;
    }

    // Clear unneded data from session
    if ( isset($_SESSION['answer']) ) unset($_SESSION['answer']);
    if ( isset($_SESSION['correct']) ) unset($_SESSION['correct']);

    $scoreBoard = scoreBoard( $pdo, $_SESSION['currentQuiz'] );
    updateInfoPane();
}

view('head'); ?>

<body class="p-5">

<?php require_once __DIR__ . '/src/inc/nav.php'; ?>
<?php require_once __DIR__ . '/src/inc/infoPane.php'; ?>

<main>
    <div id="q-card" class="container pt-3 bg-light rounded-4">

        <?php if ( isset( $_SESSION['message'] ) ) {
            echo( '<div class="text-center p-3">
                <h3 id="score" class="fs-4 bg-secondary fw-bold text-warning rounded py-1">'
                . $_SESSION['message'] . '</h3></div>' );
            unset( $_SESSION['message'] );
        } else { 
            echo  $scoreBoard; 
        } ?>

        <div id="quiz-area"></div>

    </div>
</main>

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


    function adjustSidenav() {
        const $sidenav = $('#sideNavbar');
        const $infoPane = $('#infoPane');
        if ( $(window).width() < 890 ) {
            $sidenav.width(0);
            $infoPane.width(0);
        } else {
            $sidenav.width("21%");
            $infoPane.width("21%");
        }
    }

    $(window).on('resize', adjustSidenav);

    adjustSidenav();
});
</script>

</body>
</html>