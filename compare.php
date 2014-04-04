<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
?>
<?php
    if($_POST['order']!=""){
        $_SESSION['compare_order'] = $_POST['order'];
    }
    if($_POST['order_method']!=""){
        $_SESSION['compare_order_method'] = $_POST['order_method'];
    }
    if($_POST['search']!=""){
        $_SESSION['compare_search'] = $_POST['search'];
    }
    if($_POST['keyword']!=""){
        $_SESSION['compare_keyword'] = $_POST['keyword'];
    }
    if($_POST['Clear']==1){
        unset($_SESSION['compare_keyword']);
        unset($_SESSION['compare_search']);    
        $keyword = "";
        $search = "";
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
    <h1>Comparison sheet</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <form method="POST" action="compare.php">
    <select name="order">
    <?php
        if($_SESSION['compare_order']==="id"){
            echo '<option value="id" selected>ID</option>';
        }
        else{
            echo '<option value="id">ID</option>';
        }
        if($_SESSION['compare_order']==="flight_number"){
            echo '<option value="flight_number" selected>Flight Number</option>';
        }
        else{
            echo '<option value="flight_number">Flight Number</option>';
        }
        if($_SESSION['compare_order']==="departure"){
            echo '<option value="departure" selected>Departure</option>';
        }
        else{
            echo '<option value="departure">Departure</option>';
        }
        if($_SESSION['compare_order']==="destination"){
            echo '<option value="destination" selected>Destination</option>';
        }
        else{
            echo '<option value="destination">Destination</option>';
        }
        if($_SESSION['compare_order']==="departure_date"){
            echo '<option value="departure_date" selected>Departure Date</option>';
        }
        else{
            echo '<option value="departure_date">Departure Date</option>';
        }
        if($_SESSION['compare_order']==="arrival_date"){
            echo '<option value="arrival_date" selected>Arrival Date</option>';
        }
        else{
            echo '<option value="arrival_date">Arrival Date</option>';
        }
        if($_SESSION['compare_order']==="ticket_price"){
            echo '<option value="ticket_price" selected>Ticket Price</option>';
        }
        else{
            echo '<option value="ticket_price">Ticket Price</option>';
        }
    ?>
    </select>
    <select name="order_method">
    <?php
            if($_SESSION['compare_order_method']==="ASC"){
                echo '<option value="ASC" selected>ASC</option>';
            }
            else{
                echo '<option value="ASC">ASC</option>';
            }
            if($_SESSION['compare_order_method']==="DESC"){
                echo '<option value="DESC" selected>DESC</option>';
            }
            else{
                echo '<option value="DESC">DESC</option>';
            }
    ?>
    </select>
    <button type="submit">Sort</button>
    </form>
    <form method="POST" action="compare.php">
    <select name="search">
        <?php
            if($_SESSION['compare_search']==="id"){
                echo '<option value="id" selected>ID</option>';
            }
            else{
                echo '<option value="id">ID</option>';
            }
            if($_SESSION['compare_search']==="flight_number"){
                echo '<option value="flight_number" selected>Flight Number</option>';
            }
            else{
                echo '<option value="flight_number">Flight Number</option>';
            }
            if($_SESSION['compare_search']==="departure"){
                echo '<option value="departure" selected>Departure</option>';
            }
            else{
                echo '<option value="departure">Departure</option>';
            }
            if($_SESSION['compare_search']==="destination"){
                echo '<option value="destination" selected>Destination</option>';
            }
            else{
                echo '<option value="destination">Destination</option>';
            }
            if($_SESSION['compare_search']==="departure_date"){
                echo '<option value="departure_date" selected>Departure Date</option>';
            }
            else{
                echo '<option value="departure_date">Departure Date</option>';
            }
            if($_SESSION['compare_search']==="arrival_date"){
                echo '<option value="arrival_date" selected>Arrival Date</option>';
            }
            else{
                echo '<option value="arrival_date">Arrival Date</option>';
            }
            if($_SESSION['compare_search']==="ticket_price"){
                echo '<option value="ticket_price" selected>Ticket Price</option>';
            }
            else{
                echo '<option value="ticket_price">Ticket Price</option>';
            }
        ?>
    </select>
    <input type="text" name="keyword" value="<?php echo $_SESSION['compare_keyword']; ?>"></input>
    <button type="submit" >Search</button>
    <button type="submit" name="Clear" value=1>Clear</button></br>
    </form>
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
    if($_SESSION['compare_order']!=""){
        $order = " ".$_SESSION['compare_order'];
    }
    else{
        $order = " id";
    }
    if($_SESSION['compare_order_method']!=""){
        $order_method = " ".$_SESSION['compare_order_method'];
    }
    else{
        $order_method = " ASC";
    }
    if($_SESSION['compare_keyword']!=""){
        $keyword = "%".$_SESSION['compare_keyword']."%";
    }
    
    if($_SESSION['compare_keyword']!=""){
        $sql = "SELECT * FROM `flight` "
                ."WHERE `id` IN "
                ."(SELECT `flight_ID` FROM `compare` "
                ."WHERE `account_ID` = ? ) "
                ."AND ". $_SESSION['compare_search'] ." LIKE '" . $keyword . "' "
                ."ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
    else{
        $sql = "SELECT * FROM `flight` "
                ."WHERE `id` IN "
                ."(SELECT `flight_ID` FROM `compare` "
                ."WHERE `account_ID` = ? ) "
                ."ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
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
        echo '<form action="rm_compare.php" method="post">';
        echo '<button class="btn btn-success" type="submit" name="rm_compare" value="'.$data->id.'">Remove</button>';
        echo '</form>';
        echo '</td>';
        
        echo "</tr>";
    }
?>
        </table>
</body>
</html>
