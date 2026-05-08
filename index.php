<?php
session_start();
require_once 'utils.php';

if ( isset($_POST['clear']) ) {
    clearSession();
}

if ( ! isset($_SESSION['quizIsSet']) ) {
    setQuestions();
} else {
    getQuestion();
    // var_dump($_SESSION['nextQuestion']);
}

require_once 'head.php';
?>

<div id="quiz-area"></div>
<!-- <form method="post" class="pt-4">
    <input class="btn btn-outline-danger me-3" type="submit"
        value="Clear session" name="clear">
</form> -->

<script id="quiz-template" type="text/x-handlebars-template">
    <div class="container pt-5">
        <h1>{{ question.text }}</h1>
        <div>
        <h3>{{ question.country }}</h3>
        <h5>{{ question.capital }}</h5>
        <h5>{{ question.hint }}</h5>
        <img src="{{ question.src }}" alt="" style="width:50%">
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