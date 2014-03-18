<?php
    session_save_path('./sessions');
    session_start();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Flight System</title>
    </head>

    <body>
        <h1>Welcome to flight schedule system!</h1>
        <h2>Login to use the flight schedule system!</h2><?= $_SESSION['Error'] ?>
        <form action="verify.php" method="POST">
            <br>Account : <input type="text" name="account"></br>
            <br>Password : <input type="password" name="password"></br>
            <br><button type="submit">Login</button></br>
            <h3>Don't have an account ? <a href="signup.php">Register</a></h3>
        </form>
    </body>
</html>
<?php
    unset($_SESSION['Error']);
    #$_SESSION['Error']="";
?>
