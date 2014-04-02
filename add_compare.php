<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    if($_POST['add_compare']===""){
        header("Location: main.php");
        exit();
    }
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $flight_ID = $_POST['add_compare'];

    $pre_url=$_SERVER['HTTP_REFERER'];
    $goto_url = "main.php";
    if(strpos($pre_url,"main.php")){
        $goto_url = "main.php";
    }
    if(strpos($pre_url,"user.php")){
        $goto_url = "user.php";
    }
    if(strpos($pre_url,"compare.php")){
        $goto_url = "compare.php";
    }
?>
<?php
    accessDB($db);
    $sql = "SELECT FROM `compare` "
         . "WHERE `account_ID` = ? AND `flight_ID` = ?  ";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID, $flight_ID));
    if($sth->fetchObject()){
    }
    else{
        $sql = "INSERT INTO `compare` "
             . "(account_ID, flight_ID) "
             . " VALUES(?, ?)";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($account_ID, $flight_ID));
    }
    header("Location: ".$goto_url);
    exit();

?>
