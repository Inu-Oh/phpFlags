<?php
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty($_SESSION['csrf_token']) ) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

if ( isPostRequest() ) {

    verifyCsrfOrDie();

    // Redirect to home page if user cancels login
    if ( isset($_POST['cancel']) && $_POST['cancel'] == 'Cancel') {

        header( 'Location: index.php' );
        return;
    }

    if ( isset($_POST['username']) && isset($_POST['password'])) {

        // Logout current user if any
        unset($_SESSION['username'], $_SESSION['userId']); 
        
        // Show error flash message if name or password are not entered
        if ( strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1 ) {

            $_SESSION['error'] = '<p style="color:red">User name and password are required</p>';
            header( 'Location: login.php' );
            return;
        } 

        // Lookup username
        $username = htmlentities($_POST['username']);
        $sql = "SELECT user_id, pw_hash FROM users WHERE username = :un";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(':un' => $username));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( ! empty($row) ) {

            // Validadate user password
            $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
            if ( password_verify($password, (string)$row['pw_hash'] ) ) {

                $_SESSION['username'] = $username;
                $_SESSION['userId'] = $row['user_id'];
                $_SESSION['success'] = '<p style="color:green">Logged in</p>';

                // Update any user progress made prior to login
                updateUserProgressFromSessionToDB($pdo);

                error_log("Login success for " . $username);
                header( 'Location: index.php' );
                return;
            
                } else {
                // Show error flash message if password is incorrect
                $_SESSION['error'] = '<p style="color:red">Incorrect password</p>';
                error_log("Login fail for " . $username);
                header( 'Location: login.php' );
                return;
            }
        } else {

            // Show error flash message if username is not found
            $_SESSION['error'] = '<p style="color:red">Username not found</p>';
            header( 'Location: login.php' );
            return;
        }
    }
}

if ( isGetRequest() && isset($_SESSION['username']) ) {
    header( 'Location: index.php' );
    return;
}

view('head', ['title' => 'Login']);
?>

<body class="p-5">
<main>
    <div id="q-card" class="container p-3 bg-light rounded-4">
        <h1 class="m-3"> <?php            
            if ( isset($_SESSION['error']) ) {
                echo '<span class="text-danger">'.$_SESSION['error'].'</span>';
                unset($_SESSION['error']);
            } else if ( isset($_SESSION['bug']) ) {
                echo '<span class="text-danger">'.$_SESSION['bug'].'</span>';
                unset($_SESSION['bug']);
            } else {
                echo 'Please Log In';
            }
        ?> </h1>
        <form method="POST" action="login.php" class="form-group m-3">
            <input type="hidden" name="csrf_token"
                value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-floating my-5">
                <input class="form-control" type="text" name="username" id="username">
                <label for="usernam">Username</label>
            </div>
            <div class="form-floating my-5">
                <input class="form-control" type="password" name="password" id="password">
                <label for="password">Password</label>
            </div>
            <div class="form-group">
                <input class="btn btn-outline-primary me-3" type="submit" 
                    onclick="return doValidate();" value="Log In">
                <input class="btn btn-outline-danger text-dark" type="submit" 
                    name="cancel" value="Cancel">
            </div>
        </form>
    </div>
</main>

<script>
function doValidate() {
    console.log('Validating...');
    try {
        user = document.getElementById('username').value;
        pw = document.getElementById('password').value;
        console.log("Validating addr="+user+" pw=");
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
</body>