<nav id="sideNavbar" class="sidenav bg-light">
            
<?php 
    if ( isset( $_SESSION['username'] ) ) {
        echo '<div class="card card-body m-3 p-4 bg-light border-5 border-white rounded-4">
            <div id="userId" class="card-title fs-3">
                <i class="fa-regular fa-user-circle"></i> &nbsp;' 
                    . $_SESSION['username'] . 
                '</div>
            <div class="card-text pt-2">
                <a href="logout.php">Logout</a>
            </div>
        </div>
        <div id="formCard" 
            class="card card-body m-3 bg-light border-5 border-white rounded-4">';

        echo '<div id="modesTitle" class="card-title fw-bold">Quiz modes</div>';

        if ( isset( $_SESSION['quizMode'] ) ) {

            if ( $_SESSION['quizMode'] == 'practice' ) {
                echo '<form action="switchMode.php" method="post">
                    <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                        name="csrf_token">
                    <div class="card-text">
                        <input class="nav-submit-link" type="submit" value="Learn"
                            name="learn">
                        <label class="text-secondary info-text">Discover new content</label>
                        <input class="nav-submit-link pt-3" type="submit" value="Review"
                            name="review">
                        <label class="text-secondary info-text">
                            Refresh your memory</label>
                    </div>
                </form>';

            } elseif ( $_SESSION['quizMode'] == 'review' ) { 
                echo '<form action="switchMode.php" method="post">
                    <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                        name="csrf_token">
                    <div class="card-text">
                        <input class="nav-submit-link" type="submit" value="Learn"
                            name="learn">
                        <label class="text-secondary info-text">Discover new content</label>
                        <input class="nav-submit-link pt-3" type="submit" value="Practice"
                            name="practice">
                        <label class="text-secondary info-text">
                            Strengthen skills</label>
                    </div>
                </form>';
            }
        } else {
            echo '<form action="switchMode.php" method="post">
                <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                    name="csrf_token">
                <div class="card-text">
                    <input class="nav-submit-link" type="submit" value="Practice"
                        name="practice">
                    <label class="text-secondary info-text">
                        Strengthen skills</label>
                    <input class="nav-submit-link pt-3" type="submit" value="Review"
                        name="review">
                    <label class="text-secondary info-text">
                        Refresh your memory</label>
                </div>
            </form>
        </div>';
        }
    } else {
        echo '<div class="card card-body m-3 bg-light border-5 border-white rounded-4">
            <div class="card-text">
                <a class="m-1 pt-2" href="login.php">Login</a>
                <a class="m-1 pt-2" href="register.php">Register</a>
            </div>
        </div>';
    }
?>

</nav>

<div id="infoPane" class="bg-light">
    <div class="card card-body m-3 p-4 bg-light border-5 border-white rounded-4">
        <div class="card-title fs-5 pb-1">
            Cards &nbsp;<span class="card-text fs-6"><?= $_SESSION['cardsRemaining'] ?></span>
        </div>
        <div class="card-title fs-5 py-1">
            Tested &nbsp;<span class="card-text fs-6"><?= $_SESSION['testedCards'] ?></span>
        </div>
        <div class="card-title fs-5 py-1">
            Score&nbsp;<span class="card-text fs-6">
                <?php if ( isset( $_SESSION['username'] ) ) {
                    echo $_SESSION['userCorrect'] . 
                        '</span>
                    </div>
                    <div class="card-title fs-5 py-1">Rate&nbsp
                        <span class="card-text fs-6">' . 
                            round( $_SESSION['userAccuracy'] ) . 
                        '%</span>
                    </div>
                    <div class="card-title fs-5 py-1">XP&nbsp
                        <span class="card-text fs-6">' . $_SESSION['level'];
                } else {
                    echo $_SESSION['score'];
                } ?>
            </span>
        </div>
    </div>

</div>