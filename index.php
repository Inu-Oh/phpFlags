<?php
session_start();
require_once 'utils.php';

if ( ! isset($_SESSION['quizIsSet']) ) {
    setQuestions();
} else {
    getQuestion();
    var_dump($_SESSION['nextQuestion']);
}

require_once 'head.php';
?>

<div id="quiz-area"></div>

<script id="quiz-template" type="text/x-handlebars-template">
    <div class="container pt-5">
        <h1>It's just a start</h1>
        <div>
        <h3>{{ country.country }}</h3>
        <h5>{{ country.capital }}</h5>
        <h5>{{ country.hint }}</h5>
        <img src="{{ country.src }}" alt="" style="width:50%">
        </div>
    </div>
</script>

<script>
$(document).ready(function() {
    $.getJSON('country.php', function(country) {
        window.console && console.log(country);
        var source = $('#quiz-template').html();
        var template = Handlebars.compile(source);
        var context = {};
        context.country = country;
        $('#quiz-area').replaceWith(template(context));
    }).fail( function() { alert('getJSON fail'); } );
});
</script>

</body>
</html>