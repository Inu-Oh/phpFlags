<nav id="sideNavbar" class="sidenav bg-light">
            
<?php 
    if ( isset( $_SESSION['username'] ) ) {
        echo '<div class="card card-body m-3 p-4 bg-light border-5 border-white rounded-4">
            <div id="userId" class="card-title fs-3">
                <i class="fa-regular fa-user-circle"></i> &nbsp;' 
                    . $_SESSION['username'] . 
                '</div>
            <div class="card-text py-2">
                <a href="logout.php">
                    <i class="fa-solid fa-right-from-bracket"></i>&nbsp; Logout
                </a>
            </div>
        </div>
        <div class="card card-body m-3 bg-light border-5 border-white rounded-4">';

        echo '<div id="modesTitle" class="card-title fw-bold">Quiz modes</div>
            <div class="card-text">';

        if ( isset( $_SESSION['quizMode'] ) ) {
            if ( $_SESSION['quizMode'] == 'practice' ) {
                if ( $_SESSION['testedCards'] < $_SESSION['questionCount'] ) {
                    echo '<a href="switchMode.php?mode=learn">
                            <i class="fa-solid fa-graduation-cap"></i>&nbsp; Learn
                        </a>
                        <label class="text-secondary info-text m-1 pb-2">
                            Discover new content</label>';
                }
                echo '<a href="switchMode.php?mode=review">
                        <i class="fa-solid fa-dumbbell"></i>&nbsp; Review
                    </a>
                    <label class="text-secondary info-text m-1 pb-2">
                        Refresh your memory</label>';

            } elseif ( $_SESSION['quizMode'] == 'review' ) { 
                if ( $_SESSION['testedCards'] < $_SESSION['questionCount'] ) {
                    echo '<a href="switchMode.php?mode=learn">
                            <i class="fa-solid fa-graduation-cap"></i>&nbsp; Learn
                        </a>
                        <label class="text-secondary info-text m-1 pb-2">
                            Discover new content</label>';
                }
                echo '<a href="switchMode.php?mode=practice">
                        <i class="fa-solid fa-weight-hanging"></i>&nbsp; Practice
                    </a>
                    <label class="text-secondary info-text m-1 pb-2">
                        Strengthen skills</label>';
            }
        } else {
            echo '<a href="switchMode.php?mode=practice">
                    <i class="fa-solid fa-weight-hanging"></i>&nbsp; Practice
                </a>
                <label class="text-secondary info-text m-1 pb-2">
                    Strengthen skills</label>
                <a href="switchMode.php?mode=review">
                    <i class="fa-solid fa-dumbbell"></i>&nbsp; Review
                </a>
                <label class="text-secondary info-text m-1 pb-2">
                    Refresh your memory</label>';
        }
    } else {
        echo '<div class="card card-body m-3 bg-light border-5 border-white rounded-4">
            <div class="card-text">
                <a class="py-3" href="login.php">
                    <i class="fa-solid fa-right-to-bracket"></i>&nbsp; Login
                </a>
                <a class="pb-3" href="register.php">
                    <i class="fa-regular fa-id-card"></i>&nbsp; Register
                </a>';
    }
    if ( str_contains( $_SERVER['REQUEST_URI'], 'glossary.php' ) ) {
        echo '<a href="index.php">
                <i class="fa-solid fa-person-walking-arrow-loop-left"></i> Quiz
            </a>
            <label class="text-secondary info-text m-1 pb-2">
                Return to quiz</label>';
    } elseif ( str_ends_with( $_SERVER['REQUEST_URI'], 'index.php' ) ) {
        echo '<a href="glossary.php">
                <i class="fa-solid fa-book-open-reader"></i>&nbsp; Glossary
            </a>
            <label class="text-secondary info-text m-1 pb-2">
                Review, search, learn</label>';
    }
    echo    '</div>
        </div>
    </div>';
?>

</nav>