<?php
session_save_path('./sessions');
session_start();
include_once('config.php');
    if(!$_SESSION['account']){
        header("Location: login.php");
        exit();
    }
    if($_SESSION['is_admin']){
        header("Location: main.php");
        exit();
    }
    accessDB($db);
    if($db){
        $sql = "SELECT * FROM `flight` ORDER BY id";
        $sth = $db->prepare($sql);
        $result = $sth->execute();
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to flight schedule system!</title>
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
        span {
            display:inline;
        }
        
    </style>
</head>
<body>
    <h5 class="Logout"><a href="logout.php">logout</a></h5>
    <h1>Flight System</h1>
    <table class="MainTable table table-hover table-condensed" width=800 cellspacing=2 >
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
<?php
    while ($data = $sth->fetchObject()){
        echo "<tr>";
        echo "<td>".$data->id."</td>";
        echo "<td>".$data->flight_number."</td>";
        echo "<td>".$data->departure."</td>";
        echo "<td>".$data->destination."</td>";
        echo "<td>".$data->departure_date."</td>";
        echo "<td>".$data->arrival_date."</td>";
        echo "</tr>";
    }
?>
</body>
</html>
