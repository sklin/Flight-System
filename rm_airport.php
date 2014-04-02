<?php
session_save_path('./sessions');
session_start();
include_once('config.php');
if(!$_SESSION['account']){
    header("Location: main.php");
    exit();
}
if(!$_SESSION['is_admin']){
    header("Location: main.php");
    exit();
}
$delete_ID = $_POST['delete'];
accessDB($db);
$sql = "DELETE FROM `airport` "
     . "WHERE id = ?";
$sth = $db->prepare($sql);
$result = $sth->execute(array($delete_ID));
if($result){
    header("Location: airport.php");
    exit();

}
else{
    header("Location: error.php");
    exit();
}
?>
