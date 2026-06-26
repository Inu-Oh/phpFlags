<?php
session_start();
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( is_post_request() ) {
    if ( $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }

    if ( isset($_POST['username']) && isset($_POST['email']) 
        && isset($_POST['password']) && isset($_POST['password2']) ) {
        
        $email = htmlspecialchars($_POST['email']);

        // Verify email and make sure it's unique before saving

        $username = htmlspecialchars($_POST['username']);

        // Check if username is unique / Limit name chars allowed

        if ( htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8') ==
            htmlspecialchars($_POST['password2'], ENT_QUOTES, 'UTF-8')) {
            $password = password_hash(
                htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'),
                PASSWORD_DEFAULT
            );
        } else {
            $_SESSION['flashMsg'] = "Passwords don't match";
        }
    }
}

view('head', ['title' => 'Register']);

?>
<main>
    <h1 class="fs-2">Sign Up</h1>
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