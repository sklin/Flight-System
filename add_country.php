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
$name = $_POST['name'];
$full_name = $_POST['full_name'];
if(str_replace(" ","",$name)===""){
    $_SESSION['Insert_Error'] = "Country name can not be empty!";
    header("Location: country.php");
    exit();
}
if(!preg_match("/^[A-Z][A-Z][A-Z]$/",$name)){
    $_SESSION['Insert_Error'] = "Country name has to be in 3 literals!";
    header("Location: country.php");
    exit();
}
$sql = "SELECT `name` FROM `country`"
     . " WHERE `name` = ?";
$sth = $db->prepare($sql);
$result = $sth->execute(array($name));
if($sth->fetchObject()){
    $_SESSION['Insert_Error'] = "Country name has been existed!";
    header("Location: country.php");
    exit();
}
if(str_replace(" ","",$full_name)===""){
    $_SESSION['Insert_Error'] = "Full name can not be empty!";
    header("Location: country.php");
    exit();
}

$sql = "INSERT INTO `country` (name,full_name)"
     . " VALUES( ?, ?)";
$sth = $db->prepare($sql);
$result = $sth->execute(array($name,$full_name));
if($result){
    header("Location: country.php");
    exit();

}
else{
    header("Location: error.php");
    exit();
}
?>
