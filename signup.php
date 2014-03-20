<?php
    session_save_path('./sessions');
    session_start();

    if($_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    else{
        echo <<<__HTML__
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Register!</title>
    </head>

    <body>
        <h1>Register a new account!</h1>
__HTML__;
        echo $_SESSION['Error'];
        echo <<<__HTML__
        <form action="register.php" method="POST">
            <br>Account : <input type="text" name="account"></br>
            <br>Password : <input type="password" name="password"></br>
            <br><input type="checkbox" name="is_admin" value=1> Is Admin ?</br>
            <br><button type="submit">Register!</button></br>
            <h3><a href="login.php">返回登陸頁面</a></h3>
        </form>
    </body>
</html>
__HTML__;
    }
?>

