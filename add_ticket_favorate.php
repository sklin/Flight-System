<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $flight_id_1 = $_POST['flight_id_1'];
    $flight_id_2 = $_POST['flight_id_2'];
    $flight_id_3 = $_POST['flight_id_3'];
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
    accessDB($db);
    #$sql = "SELECT FROM `ticket_favorate` "
    #     . "WHERE  user_id = ? AND flight_id_1 = ? AND flight_id_2 = ? AND flight_id_3 = ? ";
    #$sth = $db->prepare($sql);
    #$result = $sth->execute(array($account_ID, $flight_id_1, $flight_id_2, $flight_id_3));
    #if($sth->fetchObject()){
    #}
    #else{
        if($flight_id_3!="" && $flight_id_2!=""){
            $sql = "INSERT INTO ticket_favorate "
                 . "(user_id, flight_id_1, flight_id_2, flight_id_3) "
                 . " VALUES(?, ?, ?, ?)";
            $sth = $db->prepare($sql);
            $result = $sth->execute(array($account_ID, $flight_id_1, $flight_id_2, $flight_id_3));
        }
        else if($flight_id_3=="" && $flight_id_2!=""){
            $sql = "INSERT INTO ticket_favorate "
                 . "(user_id, flight_id_1, flight_id_2, flight_id_3) "
                 . " VALUES(?, ?, ?, NULL)";
            $sth = $db->prepare($sql);
            $result = $sth->execute(array($account_ID, $flight_id_1, $flight_id_2));

        }
        else{
            $sql = "INSERT INTO ticket_favorate "
                 . "(user_id, flight_id_1, flight_id_2, flight_id_3) "
                 . " VALUES(?, ?, NULL, NULL)";
            $sth = $db->prepare($sql);
            $result = $sth->execute(array($account_ID, $flight_id_1));
            
        }
    #}
    header("Location: ticket_search.php");
    exit();

?>
