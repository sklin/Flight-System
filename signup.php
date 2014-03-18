<?php
    session_save_path('./sessions');
    session_start();
?>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Register!</title>
    </head>

    <body>
        <h1>Register a new account!</h1><?= $_SESSION['Error'] ?>
        <form action="register.php" method="POST">
            <br>Account : <input type="text" name="account"></br>
            <br>Password : <input type="password" name="password"></br>
            <br><input type="checkbox" name="is_admin" value=1> Is Admin ?</br>
            <br><button type="submit">Register!</button></br>
            <h3><a href="login.php">返回登陸頁面</a></h3>
        </form>
    </body>
</html>

<?php
    unset($_SESSION['Error']);
?>
