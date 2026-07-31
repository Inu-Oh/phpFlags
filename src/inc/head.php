<!DOCTYPE html>
<html>
    <head>
        <title><?= $title ?? 'Geo Quiz' ?></title>

        <meta charset="UTF-8">  
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="static/images/favicon.ico" type="image/x-icon">
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css">
        <link rel="stylesheet" 
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
            crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
            crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-4.0.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/handlebars@latest/dist/handlebars.js"></script>
        <link rel="stylesheet" href="static/css/style.css">
    </head>
    <body class="p-5">

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

<?php # TODO - Find a good place to put this message
    if ( isset( $_SESSION['message'] ) ) {
        echo('<span class="ts-3 fw-bold text-danger">'
            . $_SESSION['message'] . '</span>');
        unset( $_SESSION['message'] );
    }
?>