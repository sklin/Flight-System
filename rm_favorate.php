<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    if($_POST['rm_favorate']===""){
        header("Location: main.php");
        exit();
    }
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $flight_ID = $_POST['rm_favorate'];

    $pre_url=$_SERVER['HTTP_REFERER'];
    $goto_url = "main.php";
    if(strpos($pre_url,"main.php")){
        $goto_url = "main.php";
    }
    if(strpos($pre_url,"user.php")){
        $goto_url = "user.php";
    }
    if(strpos($pre_url,"favorate.php")){
        $goto_url = "favorate.php";
    }
?>
<?php
    accessDB($db);
    $sql = "DELETE FROM `favorate` "
         . "WHERE `account_ID` = ? AND `flight_ID` = ? ";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID, $flight_ID));
    #var_dump($sth);
    #echo $result;
    header("Location: ".$goto_url);
    exit();

?>
