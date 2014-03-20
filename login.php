<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    if($_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    else{
        echo <<<__HTML__
<html>
    <head>
        <meta charset="utf-8">
        <title>Flight System</title>
    </head>

    <body>
        <h1>Welcome to flight schedule system!</h1>
        <h2>Login to use the flight schedule system!</h2>
__HTML__;
        echo $_SESSION['Error'];
        echo <<<__HTML__
        <form action="verify.php" method="POST">
            <br>Account : <input type="text" name="account"></br>
            <br>Password : <input type="password" name="password"></br>
            <br><button type="submit">Login</button></br>
            <h3>Don't have an account ? <a href="signup.php">Register</a></h3>
        </form>
    </body>
</html>
__HTML__;
        unset($_SESSION['Error']);
    }
?>
