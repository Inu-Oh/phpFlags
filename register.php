<?php
session_start();
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( is_post_request() ) {
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
            $_SESSION['flashMsg'] = "Passwords don<t match";
        }
    }
}

view('head', ['title' => 'Register']);

?>
<main>
    <form action="register.php" method="post">
        <h1>Sign Up</h1>
        <div>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username">
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email">
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>
        <div>
            <label for="password2">Password Again:</label>
            <input type="password" name="password2" id="password2">
        </div>
        <div>
            <label for="agree">
                <input type="checkbox" name="agree" id="agree" value="yes"/> I agree
                with the
                <a href="#" title="term of services">term of services</a>
            </label>
        </div>
        <button type="submit" name="register">Register</button>
        <footer>Already a member? <a href="login.php">Login here</a></footer>
    </form>
</main>
</body>
</html>