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
$full_name = $_POST['full_name'];
$country_id = $_POST['Country'];
$longitude = $_POST['longitude'];
$latitude = $_POST['latitude'];
$timezone = $_POST['timezone'];
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
if(str_replace(" ","",$full_name)===""){
    $_SESSION['Insert_Error'] = "Airport Full name can not be empty!";
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
$sql = "INSERT INTO `airport` (name,full_name,country_id,longitude,latitude,timezone)"
     . " VALUES(?, ?, ?, ?, ?, ?)";
$sth = $db->prepare($sql);
$result = $sth->execute(array($name,$full_name,$country_id,$longitude,$latitude,$timezone));
if($result){
    header("Location: airport.php");
    exit();

}
else{
    header("Location: error.php");
    exit();
    #echo var_dump($sth).'</br>';
    #echo var_dump($sth->errorInfo());
}
?>
