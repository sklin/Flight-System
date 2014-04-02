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
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
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
    <br><h5 class="Logout"><a href="compare.php">Comparison sheet</a></h5>
    <h1>Flight System</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <?php echo $_POST['order']; ?>
    <?php echo $_POST['order_method']; ?>
    <form method="POST" action="user.php">
    <select name="order">
        <option value="id">ID</option>
        <option value="flight_number">Flight number</option>
        <option value="departure">Departure</option>
        <option value="destination">Destination</option>
        <option value="departure_date">Departure Date</option>
        <option value="arrival_date">Arrival Date</option>
        <option value="ticket_price">Ticket Price</option>
    </select>
    <select name="order_method">
        <option value="ASC" selected>ASC</option>
        <option value="DESC">DESC</option>
    </select>
    <button type="submit">Sort</button></br>
    </form>
    <table class="MainTable table table-hover table-condensed" width=800 cellspacing=2 >
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
        <td>Ticket Price</td>
        <td class="WideTd">Compare</td>
<?php
    if($_POST['order']!=""){
        $order = " ".$_POST['order'];
    }
    else{
        $order = " id";
    }
    if($_POST['order_method']!=""){
        $order_method = " ".$_POST['order_method'];
    }
    else{
        $order_method = " ASC";
    }
?>
<?php
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
        $sql2 = "SELECT `id` FROM `compare` "
                ."WHERE `account_ID` = ?  AND `flight_ID` = ? ";
        $sth2 = $db->prepare($sql2);
        $result2 = $sth2->execute(array($account_ID,$data->id));
        if($sth2->fetchObject()){
            echo '<form action="rm_compare.php" method="post">';
            echo '<button class="btn btn-success" type="submit" name="rm_compare" value="'.$data->id.'">Remove</button>';
            echo '</form>';
        }
        else{
            echo '<form action="add_compare.php" method="post">';
            echo '<button class="btn btn-success" type="submit" name="add_compare" value="'.$data->id.'">Add</button>';
            echo '</form>';
        }
        
        echo "</tr>"."\n";
    }
?>
</body>
</html>
