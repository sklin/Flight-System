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
<html>
    <head>
        <meta charset="utf-8">
        <title>Flight System</title>
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
                padding-left: 60px;
                padding-right: 60px;
                padding-top: 30px;
                padding-bottom: 30px;
                border-radius: 15px;
            }
        </style>
    </head>

    <body>
        <h1 class="title">Welcome to Flight Schedule System!</h1>
        <div class="Login-Block">
        <strong><font color="#FF0000"><?php echo $_SESSION['Error']; ?></font></strong>
        <form class="form-signin" action="verify.php" method="POST">
            <h4>Account : <input type="text" class="form-inline" name="account"></h4>
            <h4>Password : <input type="password" class="form-inline" name="password"></h4>
            <br><button type="submit" class="btn btn-primary">Login</button>
            <h4>Don't have an account ? <a href="signup.php">Register</a></h4>
        </form>
        </div>
    </body>
</html>
<?php
    unset($_SESSION['Error']);
?>

