<?php
session_start();
require_once __DIR__ . '/src/pdo.php';
require_once __DIR__ . '/src/libs/utils.php';

if ( empty($_SESSION['csrf_token']) ) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ( isPostRequest() ) {
    if ( $_POST['csrf_token'] !== $_SESSION['csrf_token'] ) {
        die('CSRF token validation failed');
    }

    if ( isset($_POST['cancel'])) {
        if ($_POST['cancel'] == 'Cancel') {
            header('Location: index.php');
            return;
        }
    }

    if ( isset($_POST['username']) && isset($_POST['password'])) {
        unset($_SESSION['name']); # to logout current user in any
        
        if ( strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1 ) {
            $_SESSION['error'] = '<p style="color:red">User name and password are required</p>';
            header( 'Location: login.php' );
            return;
        } 

        # Lookup username if valid and get salt
        $username = htmlentities($_POST['username']);
        $stmt = $pdo->prepare("SELECT salt, pw_hash FROM users WHERE username = :un");
        $stmt->execute(array(':un' => $username));
        $saltRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( ! empty($saltRow) ) {

            # Salt hash and validadate user password
            $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
            $salt = $saltRow['salt'];
            $salted_pw = $password . $salt;
            $pw_hash = hash('sha256', $salted_pw ); 
            $sql = "SELECT * FROM users WHERE username = :un AND pw_hash = :pw";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(':un' => $username, ':pw' => $pw_hash));
            $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if ( ! empty($userRow) ) {

                $_SESSION['username'] = $userRow['username'];
                $_SESSION['userId'] = $userRow['user_id'];
                $_SESSION['success'] = '<p style="color:green">Logged in</p>';

                # Update any user progress made prior to login
                updateUserProgressFromSessionToDB($pdo);

                error_log("Login success for " . $username);
                header( 'Location: index.php' );
                return;

            } else {
                $_SESSION['error'] = '<p style="color:red">Incorrect password</p>';
                error_log("Login fail for " . $username);
                header( 'Location: login.php' );
                return;
            }
        } else {

            $_SESSION['error'] = '<p style="color:red">Username not found</p>';
            header( 'Location: login.php' );
            return;
        }
    }
}

view('head', ['title' => 'Login']);
?>

<title>Dandan Atsukunaru Login Page</title>

</head>
<body>
<div class="container p-4">
    <h1>Please Log In</h1>

    <?php
        if ( isset($_SESSION['error']) ) {
            echo '<span class="fs-4 fw-bold">' . $_SESSION['error'] . '</span>';
            unset($_SESSION['error']);
        }
    ?>
    <?php
        if ( isset($_SESSION['bug']) ) {
            echo '<span class="fs-4 fw-bold">' . $_SESSION['bug'] . '</span>';
            unset($_SESSION['bug']);
        }
    ?>
    <form method="POST" action="login.php" class="form-group col-7 pb-5">
        <input type="hidden" name="csrf_token"
            value="<?= $_SESSION['csrf_token'] ?>">
        <div class="form-floating pb-2">
            <input class="form-control" type="text" name="username" id="username">
            <label for="usernam">Username</label>
        </div>
        <div class="form-floating pb-2">
            <input class="form-control" type="password" name="password" id="password">
            <label for="password">Password</label>
        </div>
        <input class="btn btn-outline-primary me-3" type="submit" 
            onclick="return doValidate();" value="Log In">
        <input class="btn btn-outline-danger text-dark" type="submit" 
            name="cancel" value="Cancel">
    </form>
    </div>
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