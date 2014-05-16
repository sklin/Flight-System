<?php
session_save_path('./sessions');
session_start();
include_once('config.php');
    if(!$_SESSION['account']){
        header("Location: login.php");
        exit();
    }
    if(!$_SESSION['is_admin']){
        header("Location: user.php");
        exit();
    }
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $edit_id = $_POST['edit_id'];
    $name = $_POST['name'];
    $full_name = $_POST['full_name'];
?>
<?php
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
    accessDB($db);
    if(str_replace(" ","",$name)===""){
        $_SESSION['Edit_Error'] = "Country name can not be empty!";
        header("Location: country.php");
        exit();
    }
    if(!preg_match("/^[A-Z][A-Z][A-Z]$/",$name)){
        $_SESSION['Edit_Error'] = "Country name has to be in 3 literals!";
        header("Location: country.php");
        exit();
    }
    $sql = "SELECT `name` FROM `country`"
         . " WHERE `name` = ? AND NOT `id` = ? ";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($name,$edit_id));
    if($sth->fetchObject()){
        $_SESSION['Edit_Error'] = "Country name has been existed!";
        header("Location: country.php");
        exit();
    }
    if(str_replace(" ","",$name)===""){
        $_SESSION['Edit_Error'] = "Country full name can not be empty!";
        header("Location: country.php");
        exit();
    }
    $sql = "UPDATE `country` "
         . "SET `name` = ? , `full_name` = ? "
         . "WHERE `country`.`id` = ?";
    $sth = $db->prepare($sql);
    $result = $sth->execute(
        array($name,$full_name,$edit_id)
        );
    if($result){
        header("Location: country.php");
        exit();
    }
    else{
        header("Location: error.php");
        exit();
    }
?>
