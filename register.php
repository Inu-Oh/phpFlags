<?php
session_start();
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( is_post_request() ) {
    if ( $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }

    # Check that all fields are posted
    if ( isset($_POST['username']) && isset($_POST['email']) 
        && isset($_POST['password']) && isset($_POST['password2']) ) {
        
        # Validate email
        $email = htmlspecialchars($_POST['email']);
        if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL )
            || strlen($email) > 128 ) {
            $_SESSION['flashMsg'] = "Invalid email address";
            header( 'Location: register.php' );
            return;
        }
        // Make sure it's unique
        $stmt = $pdo->prepare('SELECT email FROM users WHERE email = :em');
        $stmt->execute(array(':em' => $email ));
        if ( $stmt->fetchColumn() ) {
            $_SESSION['flashMsg'] = "That email is already taken";
            header( 'Location: register.php' );
            return;
        }

        # Validate username
        $username = htmlspecialchars($_POST['username']);
        if ( strlen($username) > 32 ) {
            $_SESSION['flashMsg'] = "Choose a shorter username";
            header( 'Location: register.php' );
            return;
        }
        // Make sure it's unique
        $stmt = $pdo->prepare('SELECT username FROM users WHERE username = :un');
        $stmt->execute(array(':un' => $username));
        if ( $stmt->fetchColumn() ) {
            $_SESSION['flashMsg'] = "That username is already taken";
            header( 'Location: register.php' );
            return;
        }

        # Validate passwords. Check for match. Save as hash.
        if ( htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') ==
            htmlspecialchars($_POST['password2'], ENT_QUOTES, 'UTF-8')) {
            $pw_hash = password_hash(
                htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'),
                PASSWORD_DEFAULT
            );
        } else {
            $_SESSION['flashMsg'] = "Passwords don't match";
            header( 'Location: register.php' );
            return;
        }

        # Save new user to database and redirect to login
        $sql = 'INSERT INTO users (username, email, password)
                    VALUES(:un, :em, :pw)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':un' => $username,
            ':em' => $email,
            ':pw' => $pw_hash
        ));
        $_SESSION['user_id'] = $pdo->lastInsertId();
        header( 'Location: login.php' );
        return;
    }
}

view('head', ['title' => 'Register']);

?>
<main>
    <h1 class="fs-2">Sign Up</h1>
    <h3>
        <?php 
            if ( isset($_SESSION['flashMsg']) ) {
                echo '<span class="fs-4 fw-bold">' . $_SESSION['flashMsg'] . '</span>';
                unset($_SESSION['flashMsg']);
            }
        ?>
    </h3>
    <form action="register.php" method="post" class="form-group col-7 pb-5">
        <div class="form-floating pb-2">
            <input class="form-control" type="text" name="username" id="username">
            <label for="username">Username:</label>
        </div>
        <div class="form-floating pb-2">
            <input class="form-control" type="email" name="email" id="email">
            <label for="email">Email:</label>
        </div>
        <div class="form-floating pb-2">
            <input class="form-control" type="password" name="password" id="password">
            <label for="password">Password:</label>
        </div>
        <div class="form-floating pb-2">
            <input class="form-control" type="password" name="password2" id="password2">
            <label for="password2">Password Again:</label>
        </div>
        <button class="btn btn-success" type="submit" name="register">
            Register
        </button>
    </form>
    <footer>Already a member? <a href="login.php">Login here</a></footer>
</main>
</body>
</html>