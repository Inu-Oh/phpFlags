<?php

$quizMode = $_SESSION['quizMode'];
$quizAccuracy = $_SESSION['modeQuizAccuracy'];
$quizLength = $_SESSION['modeQuizLength'];
$correctCount = $_SESSION['modeQuizCorrect'];

switch ( $quizMode ) {
    case 'practice':
        unset( $_SESSION['practiceList'] );
        break;
    case 'review':
        unset( $_SESSION['reviewList'] );
        break;
    default:
        # code...
        break;
}

unset(
    $_SESSION['quizMode'], 
    $_SESSION['modeQuizAccuracy'],
    $_SESSION['modeQuizTested'],
    $_SESSION['modeQuizCorrect'],
    $_SESSION['modeQuizSummary']
);

getQuestion();
?>

<div class="modal fade" id="quizResultModal" tabindex="-1" 
    aria-labelledby="quizResultModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quizResultModalLabel">
                    <?= ucfirst( $quizMode ) ?> Results <?= $quizAccuracy ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Qustions tested <?= $quizLength ?><br>
                Answered correctly <?= $correctCount ?> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#quizResultModal").modal("show");
    });
</script>