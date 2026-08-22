<?php

$quizMode = $_SESSION['mode'];
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
        # TODO - Code for learn completion will go here
        break;
}

unset(
    $_SESSION['mode'], 
    $_SESSION['modeQuizAccuracy'],
    $_SESSION['modeQuizTested'],
    $_SESSION['modeQuizCorrect'],
    $_SESSION['modeQuizLength'],
    $_SESSION['modeQuizSummary']
);

$congrats = array(
    "Congrats on completing this important task—your dedication and hard work truly paid off.",
    "Well done on reaching this milestone! Your effort and focus are inspiring.",
    "You did it! Your commitment to excellence is commendable.",
    "Bravo! Your hard work has led to this fantastic achievement.",
    "Fantastic job! Your perseverance has really shone through.",
    "Congrats on finishing this quiz! Your attention to detail made all the difference.",
    "Well done on completing this quiz! Your determination is truly admirable.",
    "Kudos for your successful completion! Your skills and hard work are evident.",
    "Great work on this quiz! Your ability to stay focused is impressive.",
    "Hats off to you for completing this quiz! Your dedication is inspiring.",
    "Congrats! This achievement is just the beginning of your success.",
    "Well done! I can't wait to see where your hard work takes you next.",
    "Fantastic job! This accomplishment sets the stage for even greater things ahead.",
    "Bravo! Your success today is a stepping stone to future achievements.",
    "Congrats! Your hard work today will open doors for tomorrow."
);

getQuestion( $pdo );
?>

<div id="quizResultModal" class="modal fade" id="quizResultModal" tabindex="-1" 
    aria-labelledby="quizResultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-body p-4 m-4">

                <div class="modal-section mb-4 p-4 rounded-3 row" >
                    <h1 class="modal-title" id="quizResultModalLabel">
                        <?= ucfirst( $quizMode ) ?> Quiz Results 
                    </h1>
                    <p><?php $i = array_rand( $congrats ); echo $congrats[$i] ?></p>
                </div>
                
                <div class="modal-section fs-5 mb-4 p-4 rounded-3 row">
                    <div class="col-3 text-end pe-1">
                        <p class="fs-3"><?= $quizAccuracy ?></p>
                        <p><?= $quizLength ?></p>
                        <p><?= $correctCount ?></p>
                    </div>
                    <div class="col-9 ps-1">
                        <p class="fs-3">accuracy rate</p>
                        <p>qustions tested</p>
                        <p>answered correctly</p>
                    </div>
                </div>
            
            </div>
            
            <div class="modal-footer border-0 p-3">
                <button id="closModalBtn" type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.modal').modal('show');
    });

    $(document).keydown(function(e) {
        if(e.keyCode==13){
                $('.modal').modal('hide');
        }
    });
</script>