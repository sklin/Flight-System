<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    $account = $_POST['account'];
    $password = password_hash($_POST['password']);
    #$is_admin = $_POST['is_admin'];
    
    if(str_replace(" ","",$account)==="")
    {
        $_SESSION['Error'] = "帳號不可空白!";
        header("Location: signup.php");
        exit();
    }
    if(strpos($account," ")){
        $_SESSION['Error'] = "帳號不可含有空白!";
        header("Location: signup.php");
        exit();
    }
    if($_POST['password']==="")
    {
        #print("<br>No key password!</br>");
        $_SESSION['Error'] = "密碼不可空白!";
        header("Location: signup.php");
        exit();
    }
    /*
    if(strpos($_POST['password']," ")){
        $_SESSION['Error'] = "密碼不可含有空白!";
        header("Location: signup.php");
        exit();
    }*/

    #if($is_admin===null)
    #{
    #    #print "is_admin===null<\br>";
    #    $is_admin = 0;
    #}
    #else
    #{
    #    #print "is_admin!==null<\br>";
    #    $is_admin = 1;
    #}
    try
    {
        $dsn = "mysql:host=$db_host;dbname=$db_name";
        $db = new PDO($dsn,$db_user,$db_password);
        #echo "<br>in try</br>";
        #echo "<br>$db_name</br>";
    }
    catch (PDOException $ex)
    {
        $err_msg = $ex->getMessage();
        #echo "<br>in catch</br>";
    }
    if($db)
    {
        #echo "<br>open DB success!</br>";
        $sql = "SELECT account, password FROM `user`"
             . " WHERE `account` = ?";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account));
        if($sth->fetchObject())
        {
            #header("Location: signup.php");
            $_SESSION['Error'] = "此帳號已經有人使用了!";
            header("Location: signup.php");
            #exit();
            #print("repeated");
            exit();
        }

    #    $sql = "INSERT INTO `user` (account,password,is_admin)"
    #         . " VALUES(?, ?, ?)";
        $sql = "INSERT INTO `user` (account,password)"
             . " VALUES(?, ?)";
        $sth = $db->prepare($sql);
        if($sth)
        {
            #echo "<br>Prepare success!</br>";
        }
        else
        {
            #echo "<br>Prepare fail!</br>";
        }
    #    $result = $sth->execute(array($account,$password,$is_admin));
        $result = $sth->execute(array($account,$password));
        if($result)
        {
            #echo "<br>Execute success!</br>";
            header("Location: login.php");
            exit();

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

