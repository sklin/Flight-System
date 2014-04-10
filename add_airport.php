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
    accessDB($db);
?>
<?php
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
$name = $_POST['name'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
if(str_replace(" ","",$name)===""){
    $_SESSION['Insert_Error'] = "Airport name can not be empty!";
    header("Location: airport.php");
    exit();
}
$sql = "SELECT `name` FROM `airport`"
     . " WHERE `name` = ?";
$sth = $db->prepare($sql);
$result = $sth->execute(array($name));
if($sth->fetchObject()){
    $_SESSION['Insert_Error'] = "Airport name has been existed!";
    header("Location: airport.php");
    exit();
}
if(str_replace(" ","",$longitude)===""){
    $_SESSION['Insert_Error'] = "Longitude can not be empty!";
    header("Location: airport.php");
    exit();
}
if($longitude > 180 || $longitude < -180){
    $_SESSION['Insert_Error'] = "Longitude : -180 ~ 180";
    header("Location: airport.php");
    exit();
}
if(str_replace(" ","",$latitude)===""){
    $_SESSION['Insert_Error'] = "Latitude can not be empty!";
    header("Location: airport.php");
    exit();
}
if($latitude > 90 || $latitude < -90){
    $_SESSION['Insert_Error'] = "Latitude : -90 ~ 90";
    header("Location: airport.php");
    exit();
}
$sql = "INSERT INTO `airport` (name,longitude,latitude)"
     . " VALUES(?, ?, ?)";
$sth = $db->prepare($sql);
$result = $sth->execute(array($name,$longitude,$latitude));
if($result){
    header("Location: airport.php");
    exit();

}
else{
    header("Location: error.php");
    exit();
}
?>
