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
$delete_ID = $_POST['delete'];
accessDB($db);
$sql = "DELETE FROM `country` "
     . "WHERE id = ?";
$sth = $db->prepare($sql);
$result = $sth->execute(array($delete_ID));
if($result){
    header("Location: country.php");
    exit();

}
else{
    header("Location: error.php");
    exit();
}
?>

