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
        <script>
            function openNav() {
                $('#sideNavbar').width("23rem");
            }

            function closeNav() {
                $("#sideNavbar").width("0");
            }
        </script>
    </head>
    <body class="p-5">

        <nav id="sideNavbar" class="sidenav">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">
                &times;
            </a>
            
<?php 
    if ( isset( $_SESSION['username'] ) ) {
        echo '<span id="userId">' . $_SESSION['username'] . 
        '</span><a href="logout.php">Logout</a>';
        if ( isset( $_SESSION['username'] ) && isset( $_SESSION['quizMode'] ) ) {

            if ( $_SESSION['quizMode'] == 'practice' ) {
                echo '<form action="switchMode.php" method="post">
                    <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                        name="csrf_token">
                    <input class="nav-submit-link" type="submit" value="Learn"
                        name="learn">
                    <input class="nav-submit-link" type="submit" value="Review"
                        name="review">
                </form>';

            } else { # Case: ( $_SESSION['quizMode'] == 'review' )
                echo '<form action="switchMode.php" method="post">
                    <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                        name="csrf_token">
                    <input class="nav-submit-link" type="submit" value="Learn"
                        name="learn">
                    <input class="nav-submit-link" type="submit" value="Practice"
                        name="practice">
                </form>';
            }
        } else {
            echo '<form action="switchMode.php" method="post">
                <input type="hidden" value="' . $_SESSION['csrf_token'] . '"
                    name="csrf_token">
                <input class="nav-submit-link" type="submit" value="Practice"
                    name="practice">
                <input class="nav-submit-link" type="submit" value="Review"
                    name="review">
            </form>';
        }
    } else {
        echo '<a href="login.php">Login</a>
            <a href="register.php">Register</a>';
    }
?>

        </nav>

        <!-- <div><?php # var_dump( $_SESSION['practiceList'] , $_SESSION['userProgress']);
                    # print_r(array_keys($_SESSION));
        ?></div> -->