<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');

    if($_SESSION['account']){
        header("Location: main.php");
        exit();
    }
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Register!</title>
        <!-- Bootstrap -->
        <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <style type="text/css">
            body {
                padding-top: 20px;
                padding-bottom: 40px;
                padding-left: 50px;
                padding-right: 50px;
                background-color: #DDFFFF;
                position: absolute;
                }
            .title {
                padding-left: 0px;
            }
            .form-signin input {
                font-size: 16px;
                height: auto;
                margin-bottom: 15px;
                padding: 7px 9px;
                }
            .Login-Block {
                background-color: #F0FFFF;
                border:5px double gray;
                padding-left: 30px;
                padding-right: 30px;
                padding-top: 30px;
                padding-bottom: 30px;
                border-radius: 15px;
            }
        </style>
    </head>

    <body>
        <h1>Register a new account!</h1>
        <strong><font color="#FF0000"><?php echo $_SESSION['Error']; ?></font></strong>
        <div class="Login-Block">
        <form class="form-signin" action="register.php" method="POST">
            <h4>Account : <input type="text" name="account"></h4>
            <h4>Password : <input type="password" name="password"></h4>
            <br><button class="btn btn-primary" type="submit">Register!</button></br>
            <h4><a href="login.php">返回登陸頁面</a></h3>
        </form>
        </div>
    </body>
</html>
<?php
    unset($_SESSION['Error']);
?>
