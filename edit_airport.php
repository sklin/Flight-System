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
    $country_id = $_POST['Country'];
    $longitude = $_POST['longitude'];
    $latitude = $_POST['latitude'];
    $timezone = $_POST['timezone'];
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
        $_SESSION['Edit_Error'] = "Airport name can not be empty!";
        header("Location: airport.php");
        exit();
    }
    $sql = "SELECT `name` FROM `airport`"
         . " WHERE `name` = ? AND NOT `id` = ? ";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($name,$edit_id));
    if($sth->fetchObject()){
        $_SESSION['Edit_Error'] = "Airport name has been existed!";
        header("Location: airport.php");
        exit();
    }
    if(str_replace(" ","",$full_name)===""){
        $_SESSION['Edit_Error'] = "Airport Full name can not be empty!";
        header("Location: airport.php");
        exit();
    }
    if(str_replace(" ","",$longitude)===""){
        $_SESSION['Edit_Error'] = "Longitude can not be empty!";
        header("Location: airport.php");
        exit();
    }
    if($longitude > 180 || $longitude < -180){
        $_SESSION['Edit_Error'] = "Longitude : -180 ~ 180";
        header("Location: airport.php");
        exit();
    }
    if(str_replace(" ","",$latitude)===""){
        $_SESSION['Edit_Error'] = "Latitude can not be empty!";
        header("Location: airport.php");
        exit();
    }
    if($latitude > 90 || $latitude < -90){
        $_SESSION['Edit_Error'] = "Latitude : -90 ~ 90";
        header("Location: airport.php");
        exit();
    }
    $sql = "UPDATE `airport` "
         . "SET `name` = ? , `full_name` = ?, `country_id` = ?, `longitude` = ? , `latitude` = ?, `timezone` = ? "
         . "WHERE `airport`.`id` = ?";
    $sth = $db->prepare($sql);
    $result = $sth->execute(
        array($name,$full_name,$country_id,$longitude,$latitude,$timezone,$edit_id)
        );
    if($result){
        header("Location: airport.php");
        exit();

    }
    else{
        header("Location: error.php");
        exit();
    }
?>
