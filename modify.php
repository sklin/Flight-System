<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    if(count($_POST)==0){//POST ???
        #print '$_POST == 0';
        header("Location: main.php");
        exit();
    }
    else if($_POST['modify']!=1){
        #print 'modify != 1';
        header("Location: main.php");
        exit();
    }
    else{
        $flight_number = $_POST['flight_number'];
        $departure = $_POST['departure'];
        $destination = $_POST['destination'];
        $departure_date = $_POST['departure_date'];
        $arrival_date = $_POST['arrival_date'];
        if(strpos($_POST['flight_number']," ")){
            header("Location: main.php");
            $_SESSION['Edit_Error'] = "Flight Number不可含有空白";
            exit();
        }
        if(strpos($_POST['departure']," ")){
            header("Location: main.php");
            $_SESSION['Edit_Error'] = "Departure不可含有空白";
            exit();
        }
        if(strpos($_POST['destination']," ")){
            header("Location: edit.php");
            $_SESSION['main_Error'] = "Destination不可含有空白";
            exit();
        }
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "UPDATE `flight` "
                 . "SET `flight_number` = ? , `departure` = ? , `destination` = ? , `departure_date` = ? , `arrival_date` = ? "
                 . "WHERE `flight`.`id` = ?";
            $sth = $db->prepare($sql);
            $result = $sth->execute(
                array($flight_number,$departure,$destination,$departure_date,$arrival_date,$_POST['id'])
                );
            
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
