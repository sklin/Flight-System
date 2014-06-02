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
    #print count($_POST);
    #if(count($_POST)==0){//POST ???
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    if(!$_SESSION['is_admin']){
        header("Location: main.php");
        exit();
    }
    if($_POST['insert']!=1){//POST ???
        #print '$_POST == 0';
        header("Location: main.php");
        exit();
    }
    else{
        $flight_number = $_POST['flight_number'];
        $departure = $_POST['departure'];
        $destination = $_POST['destination'];
        $departure_time = $_POST['departure_time'];
        $arrival_time = $_POST['arrival_time'];
        $ticket_price = $_POST['ticket_price'];
        if(str_replace(" ","",$flight_number)===""){
            $_SESSION['Insert_Error'] = "Flight Number cannot be empty!";
            header("Location: main.php");
            exit();
        }
        else if(str_replace(" ","",$departure)===""){
            $_SESSION['Insert_Error'] = "Departure cannot be empty!";
            header("Location: main.php");
            exit();
        }
        else if(str_replace(" ","",$destination)===""){
            $_SESSION['Insert_Error'] = "Destination cannot be empty!";
            header("Location: main.php");
            exit();
        }
        else if(str_replace(" ","",$departure_time)===""){
            $_SESSION['Insert_Error'] = "Departure Date cannot be empty!";
            header("Location: main.php");
            exit();
        }
        else if(str_replace(" ","",$arrival_time)===""){
            $_SESSION['Insert_Error'] = "Arrival Date cannot be empty!";
            header("Location: main.php");
            exit();
        }
        else if(str_replace(" ","",$ticket_price)===""){
            $_SESSION['Insert_Error'] = "Ticket price cannot be empty!";
            header("Location: main.php");
            exit();
        }
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "INSERT INTO `flight` "
                 . "(flight_number,departure,destination,departure_date,arrival_date,ticket_price)"
                 . " VALUES(?, ?, ?, ?, ?, ?)";
            $sth = $db->prepare($sql);
            $result = $sth->execute(
                array($flight_number,$departure,$destination,$departure_time,$arrival_time,$ticket_price)
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
