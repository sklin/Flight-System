<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
?>
<?php
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    accessDB($db);
    $sql = "SELECT id, account FROM `user`"
         . " WHERE `id` = ? AND `account` = ?";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID,$account));
    if(!$sth->fetchObject()){
        header("Location: logout.php");
        exit();
    }
?>
<?php
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    if(!$_SESSION['is_admin']){
        header("Location: main.php");
        exit();
    }

    if($_POST['add']!=1){
        header("Location: authority.php");
        exit();
    }
    accessDB($db);


    $account = $_POST['account'];
    $password = password_hash($_POST['password']);
    $is_admin = $_POST['is_admin'];
    if(str_replace(" ","",$account)==="")
    {
        $_SESSION['Error'] = "帳號不可空白!";
        header("Location: authority.php");
        exit();
    }
    if(strpos($account," ")){
        $_SESSION['Error'] = "帳號不可含有空白!";
        header("Location: authority.php");
        exit();
    }
    if($_POST['password']==="")
    {
        #print("<br>No key password!</br>");
        $_SESSION['Error'] = "密碼不可空白!";
        header("Location: authority.php");
        exit();
    }
    /*
    if(strpos($_POST['password']," ")){
        $_SESSION['Error'] = "密碼不可含有空白!";
        header("Location: authority.php");
        exit();
    }*/
    if($is_admin===null)
    {
        #print "is_admin===null<\br>";
        $is_admin = 0;
    }
    else
    {
        #print "is_admin!==null<\br>";
        $is_admin = 1;
    }

    # Check repeated
    $sql = "SELECT account, password FROM `user`"
         . " WHERE `account` = ?";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account));
    if($sth->fetchObject())
    {
        $_SESSION['Error'] = "此帳號已經有人使用了!";
        header("Location: authority.php");
        exit();
    }
    else{
    # Add user
        $sql = "INSERT INTO `user` (account,password,is_admin)"
             . " VALUES(?, ?, ?)";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account,$password,$is_admin));
        header("Location: authority.php");
        exit();
    }
?>
