<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');

    $account = $_POST['account'];
    $password = $_POST['password'];

    if(!$account)
    {
        #print("<br>Please type in your account</br>");
        $_SESSION['Error'] = "帳號不可空白!";
        header("Location: login.php");
        exit();
    }
    if(!$password)
    {
        #print("<br>No password</br>");
        $_SESSION['Error'] = "密碼不可空白!";
        header("Location: login.php");
        exit();
    }
    try
    {
        $dsn = "mysql:host=$db_host;dbname=$db_name";
        $db = new PDO($dsn,$db_user,$db_password);
    }
    catch (PDOException $ex)
    {
        $err_msg = $ex->getMessage();
    }
    if($db)
    {
#        $sql = "SELECT account, password FROM `user`"
#             . " WHERE `account` = ? AND `password` = ?";
        $sql = "SELECT account, password, is_admin FROM `user`"
             . " WHERE `account` = ?";
#             . " WHERE `account` = ? AND `password` = ?";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account));
        if($result)
        {
            #echo "<br>Execute success!</br>";
            #header("Location: login.php");
            
            $temp = $sth->fetchObject();
#            $hash = $temp->password
            if(password_verify($password,$temp->password))
            {
                $_SESSION['account'] = $_POST['account'];
                $_SESSION['is_admin'] = $temp->is_admin;
                header("Location: main.php");
                exit();
            }
            else
            {
                $_SESSION['Error'] = "密碼錯誤!";
                header("Location: login.php");
                exit();
            }
        }
        else
        {
            #echo "<br>Execute fail!</br>";
            #print_r( $sth->errorInfo());
            header("Location: error.php");
            exit();
        }

    }
    else
    {
        #echo "<br>open DB fail!</br>";
        header("Location: error.php");
        exit();
    }

?>
