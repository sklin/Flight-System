<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
?>
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
    <h5 class="Logout"><a href="main.php">back</a></h5>
    <h1>Favorate</h1>
        <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
            <tr>
            <td>#</td>
            <td>Flight Number</td>
            <td>Departure</td>
            <td>Destination</td>
            <td>Departure Date</td>
            <td>Arrival Date</td>
            <td>Ticket Price</td>
            <td class="WideTd">Remove</td>
            </tr>
<?php
    accessDB($db);
    
    $sql = "SELECT * FROM `flight` "
            ."WHERE `id` IN "
            ."(SELECT `flight_ID` FROM `favorate` "
            ."WHERE `account_ID` = ? ) ";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($_SESSION['account_ID']));
    echo '<tr>';
    while ($data = $sth->fetchObject()){
        echo "<tr>";
        echo "<td>".$data->id."</td>";
        echo "<td>".$data->flight_number."</td>";
        echo "<td>".$data->departure."</td>";
        echo "<td>".$data->destination."</td>";
        echo "<td>".$data->departure_date."</td>";
        echo "<td>".$data->arrival_date."</td>";
        echo "<td>".$data->ticket_price."</td>";

        echo '<td>';
        echo '<form action="rm_favorate.php" method="post">';
        echo '<button class="btn btn-success" type="submit" name="rm_favorate" value="'.$data->id.'">Remove</button>';
        echo '</form>';
        echo '</td>';
        
        echo "</tr>";
    }
?>
        </table>
</body>
</html>
