<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
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
