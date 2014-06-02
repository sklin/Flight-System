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
    #else if($_POST['modify']!=1){
    else if(!$_POST['edit_id']){
        #print 'modify != 1';
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
        if(str_replace(" ","",$_POST['flight_number'])===""){
            header("Location: main.php");
            $_SESSION['Edit_Error'] = "Flight Number cannot be empty!";
            exit();
        }
        if(str_replace(" ","",$_POST['departure'])===""){
            header("Location: main.php");
            $_SESSION['Edit_Error'] = "Departure cannot be empty!";
            exit();
        }
        if(str_replace(" ","",$_POST['destination'])===""){
            header("Location: edit.php");
            $_SESSION['Edit_Error'] = "Destination cannot be empty!";
            exit();
        }
        if(str_replace(" ","",$_POST['departure_time'])===""){
            header("Location: edit.php");
            $_SESSION['Edit_Error'] = "Departure Date cannot be empty!";
            exit();
        }
        if(str_replace(" ","",$_POST['arrival_time'])===""){
            header("Location: edit.php");
            $_SESSION['Edit_Error'] = "Arrival Date cannot be empty!";
            exit();
        }
        if(str_replace(" ","",$_POST['ticket_price'])===""){
            header("Location: edit.php");
            $_SESSION['Edit_Error'] = "Ticket price cannot be empty!";
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
                 . "SET `flight_number` = ? , `departure` = ? , `destination` = ? , `departure_date` = ? , `arrival_date` = ? , `ticket_price` = ? "
                 . "WHERE `flight`.`id` = ?";
            $sth = $db->prepare($sql);
            $result = $sth->execute(
                array($flight_number,$departure,$destination,$departure_time,$arrival_time,$ticket_price,$_POST['edit_id'])
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
