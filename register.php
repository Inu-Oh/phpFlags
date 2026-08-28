<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty( $_SESSION['csrf_token'] ) ) 
    $_SESSION['csrf_token'] = bin2hex( random_bytes( 32 ) );

if ( isPostRequest() ) {
    
    verifyCsrfOrDie();

    // Check that all fields are posted
    if ( isset( $_POST['username'] ) && isset( $_POST['email'] ) 
        && isset( $_POST['password'] ) && isset( $_POST['password2'] ) ) {
        
        // Validate email
        $email = htmlspecialchars( $_POST['email'] );
        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL )
            || strlen( $email ) > 128 ) {
            $_SESSION['error'] = "Invalid email address";
            header( 'Location: register.php' );
            return;
        }
        // Make sure email is unique
        $stmt = $pdo->prepare('SELECT email FROM users WHERE email = :em');
        $stmt->execute( array( ':em' => $email ) );
        if ( $stmt->fetchColumn() ) {
            $_SESSION['error'] = "That email is already taken";
            header( 'Location: register.php' );
            return;
        }

        // Validate username
        $username = htmlspecialchars( $_POST['username'] );
        if ( strlen($username) > 32 ) {
            $_SESSION['error'] = "Choose a shorter username";
            header( 'Location: register.php' );
            return;
        }
        // Make sure username is unique
        $stmt = $pdo->prepare( 'SELECT username FROM users WHERE username = :un' );
        $stmt->execute( array( ':un' => $username ) );
        if ( $stmt->fetchColumn() ) {
            $_SESSION['error'] = "That username is already taken";
            header( 'Location: register.php' );
            return;
        }

        // Validate password. Check for match.
        if ( htmlspecialchars( $_POST['password'], ENT_QUOTES, 'UTF-8' ) ==
            htmlspecialchars( $_POST['password2'], ENT_QUOTES, 'UTF-8' ) ) {
            
            // Hash the password before saving
            $password = htmlspecialchars( $_POST['password'], ENT_QUOTES, 'UTF-8' );
            $options = [ 'cost' => 12 ];
            $pw_hash = password_hash( $password, PASSWORD_BCRYPT, $options ); 

            // Save new user data and hash to database
            $sql = 'INSERT INTO users (username, email, pw_hash) VALUES(:un, :em, :pw)';
            $stmt = $pdo->prepare( $sql );
            $stmt->execute( array(
                ':un' => $username,
                ':em' => $email,
                ':pw' => $pw_hash,
            ) );
            $_SESSION['userId'] = $pdo->lastInsertId();

            // Reset all quiz lists. Initiate user's progress for each quiz question.
            setQuestions( $pdo );
            $quizzes = quizArray();
            foreach ( $quizzes as $quizName => $quizId ) { 
                foreach ( $_SESSION[$quizName] as $countryId ) {
                    $sql = 'INSERT INTO progress (user_id, country_id, quiz_id)
                                VALUES (:ui, :ci, :qi)';
                    $stmt = $pdo->prepare( $sql );
                    $stmt->execute( array(
                        ':ui' => $_SESSION['userId'],
                        ':ci' => $countryId,
                        ':qi' => $quizId
                    ) );
                }
            }

            // Update the user prgross in DB based on progress saved in session
            updateUserProgressFromSessionToDB( $pdo );
            unset( $_SESSION['userId'] );

            header( 'Location: login.php' );
            return;

        } else {

            $_SESSION['error'] = "Passwords don't match";
            header( 'Location: register.php' );
            return;
        }
    }
}

if ( isGetRequest() && isset( $_SESSION['username'] ) ) {
    header( 'Location: index.php' );
    return;
}

view( 'head', ['title' => 'Register'] );
?>
<body class="p-5 bg-light">
<main>
    <div id="q-card"
        class="container p-3 bg-light border border-5 border-white rounded-4">
        <h1 class="m-3"> <?php            
            if ( isset( $_SESSION['error'] ) ) {
                echo '<span class="text-danger">' . $_SESSION['error'] . '</span>';
                unset( $_SESSION['error'] );
            } else if ( isset($_SESSION['bug']) ) {
                echo '<span class="text-danger">' . $_SESSION['bug'] . '</span>';
                unset( $_SESSION['bug'] );
            } else {
                echo 'Sign Up';
            }
        ?> </h1>
        <form action="register.php" method="post" class="form-group m-3">
            <input type="hidden" name="csrf_token"
                value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-floating my-4">
                <input class="form-control" type="text" name="username" id="username">
                <label for="username">Username</label>
            </div>
            <div class="form-floating my-4">
                <input class="form-control" type="email" name="email" id="email">
                <label for="email">Email</label>
            </div>
            <div class="form-floating my-4">
                <input class="form-control" type="password" name="password" id="password">
                <label for="password">Password</label>
            </div>
            <div class="form-floating my-4">
                <input class="form-control" type="password" name="password2" id="password2">
                <label for="password2">Password Again</label>
            </div>
            <button class="btn btn-success" type="submit" name="register">
                Register
            </button>
            <div class="my-4">Already a member? <a href="login.php">Login here</a></div>
        </form>
    </div>
</main>
</body>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        user = document.getElementById('username').value;
        pw = document.getElementById('password').value;
        console.log("Validating addr=" + user + " pw=");
        if (user == null || user == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</html>