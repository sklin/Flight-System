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
<?php
    if($_POST['order']!=""){
        $_SESSION['user_order'] = $_POST['order'];
    }
    if($_POST['order_method']!=""){
        $_SESSION['user_order_method'] = $_POST['order_method'];
    }
    if($_POST['search']!=""){
        $_SESSION['user_search'] = $_POST['search'];
    }
    if($_POST['keyword']!=""){
        $_SESSION['user_keyword'] = $_POST['keyword'];
    }
    if($_POST['Clear']==1){
        unset($_SESSION['user_keyword']);
        unset($_SESSION['user_search']);    
        $keyword = "";
        $search = "";
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
            left: 80%;
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
    <form method="POST" action="user.php">
    <select name="order">
    <?php
        if($_SESSION['user_order']==="id"){
            echo '<option value="id" selected>ID</option>';
        }
        else{
            echo '<option value="id">ID</option>';
        }
        if($_SESSION['user_order']==="flight_number"){
            echo '<option value="flight_number" selected>Flight Number</option>';
        }
        else{
            echo '<option value="flight_number">Flight Number</option>';
        }
        if($_SESSION['user_order']==="departure"){
            echo '<option value="departure" selected>Departure</option>';
        }
        else{
            echo '<option value="departure">Departure</option>';
        }
        if($_SESSION['user_order']==="destination"){
            echo '<option value="destination" selected>Destination</option>';
        }
        else{
            echo '<option value="destination">Destination</option>';
        }
        if($_SESSION['user_order']==="departure_date"){
            echo '<option value="departure_date" selected>Departure Date</option>';
        }
        else{
            echo '<option value="departure_date">Departure Date</option>';
        }
        if($_SESSION['user_order']==="arrival_date"){
            echo '<option value="arrival_date" selected>Arrival Date</option>';
        }
        else{
            echo '<option value="arrival_date">Arrival Date</option>';
        }
        if($_SESSION['user_order']==="ticket_price"){
            echo '<option value="ticket_price" selected>Ticket Price</option>';
        }
        else{
            echo '<option value="ticket_price">Ticket Price</option>';
        }
    ?>
    </select>
    <select name="order_method">
    <?php
            if($_SESSION['user_order_method']==="ASC"){
                echo '<option value="ASC" selected>ASC</option>';
            }
            else{
                echo '<option value="ASC">ASC</option>';
            }
            if($_SESSION['user_order_method']==="DESC"){
                echo '<option value="DESC" selected>DESC</option>';
            }
            else{
                echo '<option value="DESC">DESC</option>';
            }
    ?>
    </select>
    <button type="submit">Sort</button>
    </form>
    <form method="POST" action="user.php">
    <select name="search">
        <?php
            if($_SESSION['user_search']==="id"){
                echo '<option value="id" selected>ID</option>';
            }
            else{
                echo '<option value="id">ID</option>';
            }
            if($_SESSION['user_search']==="flight_number"){
                echo '<option value="flight_number" selected>Flight Number</option>';
            }
            else{
                echo '<option value="flight_number">Flight Number</option>';
            }
            if($_SESSION['user_search']==="departure"){
                echo '<option value="departure" selected>Departure</option>';
            }
            else{
                echo '<option value="departure">Departure</option>';
            }
            if($_SESSION['user_search']==="destination"){
                echo '<option value="destination" selected>Destination</option>';
            }
            else{
                echo '<option value="destination">Destination</option>';
            }
            if($_SESSION['user_search']==="departure_date"){
                echo '<option value="departure_date" selected>Departure Date</option>';
            }
            else{
                echo '<option value="departure_date">Departure Date</option>';
            }
            if($_SESSION['user_search']==="arrival_date"){
                echo '<option value="arrival_date" selected>Arrival Date</option>';
            }
            else{
                echo '<option value="arrival_date">Arrival Date</option>';
            }
            if($_SESSION['user_search']==="ticket_price"){
                echo '<option value="ticket_price" selected>Ticket Price</option>';
            }
            else{
                echo '<option value="ticket_price">Ticket Price</option>';
            }
        ?>
    </select>
    <input type="text" name="keyword" value="<?php echo $_SESSION['user_keyword']; ?>"></input>
    <button type="submit" >Search</button>
    <button type="submit" name="Clear" value=1>Clear</button></br>
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
    if($_SESSION['user_order']!=""){
        $order = " ".$_SESSION['user_order'];
    }
    else{
        $order = " id";
    }
    if($_SESSION['user_order_method']!=""){
        $order_method = " ".$_SESSION['user_order_method'];
    }
    else{
        $order_method = " ASC";
    }
?>
<?php
    if($_SESSION['user_order']!=""){
        $order = " ".$_SESSION['user_order'];
    }
    else{
        $order = " id";
    }
    if($_SESSION['user_order_method']!=""){
        $order_method = " ".$_SESSION['user_order_method'];
    }
    else{
        $order_method = " ASC";
    }
    if($_SESSION['user_keyword']!=""){
        $keyword = "%".$_SESSION['user_keyword']."%";
    }
    
    if($_SESSION['user_keyword']!=""){
        $sql = "SELECT * FROM `flight` "
                ."WHERE ". $_SESSION['user_search'] ." LIKE '" . $keyword . "' "
                ."ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
    else{
        $sql = "SELECT * FROM `flight` ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute();

    }
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
