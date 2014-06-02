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
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $flight_id_1 = $_POST['flight_id_1'];
    $flight_id_2 = $_POST['flight_id_2'];
    $flight_id_3 = $_POST['flight_id_3'];

    $pre_url=$_SERVER['HTTP_REFERER'];
    $goto_url = "ticket_search.php";
    if(strpos($pre_url,"ticket_search.php")){
        $goto_url = "ticket_search.php";
    }
    if(strpos($pre_url,"compare.php")){
        $goto_url = "compare.php";
    }
?>
<?php
    accessDB($db);
    if($flight_id_2!="" && $flight_id_3!=""){
        $sql = "DELETE FROM ticket_favorate "
             . "WHERE user_id = ? AND flight_id_1 = ? AND flight_id_2 = ? AND flight_id_3 = ?";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account_ID, $flight_id_1, $flight_id_2, $flight_id_3));
    }
    else if($flight_id_2!="" && $flight_id_3==""){
        $sql = "DELETE FROM ticket_favorate "
             . "WHERE user_id = ? AND flight_id_1 = ? AND flight_id_2 = ? AND flight_id_3 IS NULL ";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account_ID, $flight_id_1, $flight_id_2));
    }
    else{//($flight_id_2=="" && $flight_id_3=="")
        $sql = "DELETE FROM ticket_favorate "
             . "WHERE user_id = ? AND flight_id_1 = ? AND flight_id_2 IS NULL AND flight_id_3 IS NULL";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account_ID, $flight_id_1));
    }
    #var_dump($sth);
    #echo $result;
    header("Location: ".$goto_url);
    exit();

?>
