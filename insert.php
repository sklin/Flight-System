<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    #print count($_POST);
    if(count($_POST)==0){//POST ???
        #print '$_POST == 0';
        header("Location: main.php");
        exit();
    }
    else{
        $flight_number = $_POST['flight_number'];
        $departure = $_POST['departure'];
        $destination = $_POST['destination'];
        $departure_date = $_POST['departure_date'];
        $arrival_date = $_POST['arrival_date'];
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "INSERT INTO `flight` "
                 . "(flight_number,departure,destination,departure_date,arrival_date)"
                 . " VALUES(?, ?, ?, ?, ?)";
            $sth = $db->prepare($sql);
            $result = $sth->execute(
                array($flight_number,$departure,$destination,$departure_date,$arrival_date)
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
    unset($_SESSION['insert']);
?>
