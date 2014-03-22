<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    if(count($_POST)==0){//POST ???
    #if(count($_POST)==0 && !$_SESSION['Edit_Error']){//POST ???
        #print '$_POST == 0';
        header("Location: main.php");
        exit();
    }
    else{
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "SELECT * FROM `flight` WHERE `id` = ?";
            $sth = $db->prepare($sql);
            $result = $sth->execute(array($_POST['Edit']));
            
            echo <<<__HTML__
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <style type=text/css>
        body {
            padding-top: 20px;
            padding-bottom: 40px;
            padding-left: 50px;
            padding-right: 50px;
            }
        .MainTable {
            font-size: 18px;
        }
        .Logout{
            font-size: 20px;
            position: absolute;
            left: 90%;
            padding-right: 50px;
        }
        .Error{
            font-size: 20px;
        }
        span {
            display:inline;
        }
        
    </style>
</head>
<body>
    <h1>Edit</h1>
__HTML__;
#            echo $_SESSION['Edit_Error'].'</br>';
#            unset($_SESSION['Edit_Error']);
            echo <<<__HTML__
  <form action="modify.php" method="POST">
    <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
        <tr>
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
        </tr>
__HTML__;
            $data = $sth->fetchObject();
            echo '<tr>';
            echo '<td>'.$data->id.'</td>';
            echo '<td><input type="text" name="flight_number" value='.$data->flight_number.'></td>';
            echo '<td><input type="text" name="departure" value='.$data->departure.'></td>';
            echo '<td><input type="text" name="destination" value='.$data->destination.'></td>';
            echo '<td><input type="datetime-local" name="departure_date" value='.$data->departure_date.'></td>';
            echo '<td><input type="datetime-local" name="arrival_date" value='.$data->arrival_date.'></td>';
            echo '</table>';
            echo '<input type="hidden" name="id" value='.$_POST['Edit'].'>';
            echo <<<__HTML__
    <br><button class="btn btn-success" type="submit" name="modify" value=1>Confirm</button>
    <button class="btn btn-success" type="submit" name="modify" value=0>Cancel</button>
  </form>
__HTML__;

            echo <<<__HTML__
</body>
</html>
__HTML__;

        }
    }
?>
