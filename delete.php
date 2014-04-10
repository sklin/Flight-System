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
    if(!$_SESSION['is_admin']){
        header("Location: main.php");
        exit();
    }
    if(count($_POST)==0){//POST ???
        #print '$_POST == 0';
        header("Location: main.php");
        exit();
    }
    else{
        $delete_ID = $_POST['Delete'];
        #echo $delete_ID;
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "DELETE FROM `flight` "
                 . "WHERE id = ?";
            $sth = $db->prepare($sql);
            $result = $sth->execute(array($delete_ID));
            
            if($result){
                #echo "<br>Execute success!</br>";
                header("Location: main.php");
                exit();

            }
            else{
                #echo "<br>Execute fail!</br>";
                #print_r( $sth->errorInfo());
                header("Location: error.php");
                exit();
            }
        }
    }
?>
