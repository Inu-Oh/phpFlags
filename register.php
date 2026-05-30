<?php
session_start();
require_once 'src/pdo.php';
require_once 'src/libs/utils.php';

if ( isset($_POST['register']) ) {
    if ( isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) ) {
        $username = htmlspecialchars($_POST['username']);
        $email = htmlspecialchars($_POST['email']);
        $password = password_hash(
            htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'),
            PASSWORD_DEFAULT
        );
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
        <button type="submit">Register</button>
        <footer>Already a member? <a href="login.php">Login here</a></footer>
    </form>
</main>
</body>
</html>