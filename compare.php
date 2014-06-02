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
    <h5 class="Logout"><a href="logout.php">logout</a></h5>
    <h1>Comparison sheet</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <ul class="nav nav-tabs">
        <li><a href="main.php"><i class="icon-home"></i> Home</a></li>
    <?php
        if($_SESSION['is_admin']==1){
            echo '<li><a href="authority.php"><i class="icon-user"></i> User List</a></li>';
            echo '<li><a href="airport.php"><i class="icon-plane"></i> Airport List</a></li>';
            echo '<li><a href="country.php"><i class="icon-globe"></i> Country List</a></li>';
        }
    ?>
        <li><a href="ticket_search.php"><i class="icon-ok-circle"></i> Ticket Search</a></li>
        <li class="active"><a href="compare.php"><i class="icon-heart"></i> Comparison Sheet</a></li>
    </ul>
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
                ."ORDER BY " . $order . $order_method . ", flight_number ";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
    else{
        $sql = "SELECT * FROM `flight` "
                ."WHERE `id` IN "
                ."(SELECT `flight_ID` FROM `compare` "
                ."WHERE `account_ID` = ? ) "
                ."ORDER BY " . $order . $order_method . ", flight_number ";
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
        <br></br>
        <h3>Not in comparison sheet</h3>
        <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
            <tr>
            <td>#</td>
            <td>Flight Number</td>
            <td>Departure</td>
            <td>Destination</td>
            <td>Departure Date</td>
            <td>Arrival Date</td>
            <td>Ticket Price</td>
            <td class="WideTd">Add</td>
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
                ."WHERE `id` NOT IN "
                ."(SELECT `flight_ID` FROM `compare` "
                ."WHERE `account_ID` = ? ) "
                ."AND ". $_SESSION['compare_search'] ." LIKE '" . $keyword . "' "
                ."ORDER BY " . $order . $order_method . ", flight_number ";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
    else{
        $sql = "SELECT * FROM `flight` "
                ."WHERE `id` NOT IN "
                ."(SELECT `flight_ID` FROM `compare` "
                ."WHERE `account_ID` = ? ) "
                ."ORDER BY " . $order . $order_method . ", flight_number ";
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
        echo '<form action="add_compare.php" method="post">';
        echo '<button class="btn btn-success" type="submit" name="add_compare" value="'.$data->id.'">Add</button>';
        echo '</form>';
        echo '</td>';
        
        echo "</tr>";
    }
?>
        </table>

        <table class="MainTable table-bordered table table-hover table-condensed" border="1">
            <tr>
            <th>Flight Number</th>
            <th>Departure Airport</th>
            <th>Destination Airport</th>
            <th>Departure Time</th>
            <th>Arrival Time</th>
            <th>Price</th>
            <th>Remove</th>
            </tr>
<?php
    $sql = <<<__SQL__
    SELECT DISTINCT
    flight_1.id AS flight_1_id,
    flight_1.flight_number AS flight_1_flight_number,
    flight_1.departure AS flight_1_departure,
    flight_1.destination AS flight_1_destination,
    flight_1.departure_date AS flight_1_departure_date,
    flight_1.arrival_date AS flight_1_arrival_date,

    flight_1.ticket_price
        AS total_price

    FROM ticket_favorate
    JOIN flight AS flight_1 ON flight_1.id = ticket_favorate.flight_id_1
    WHERE ticket_favorate.user_id = ? AND ticket_favorate.flight_id_2 IS NULL AND ticket_favorate.flight_id_3 IS NULL
__SQL__;
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID));
    while($data = $sth->fetchObject()){
        echo <<<__HTML__
        <tr>
            <td>{$data->flight_1_flight_number}</td>
            <td>{$data->flight_1_departure}</td>
            <td>{$data->flight_1_destination}</td>
            <td>{$data->flight_1_departure_date}</td>
            <td>{$data->flight_1_arrival_date}</td>
            <td>{$data->total_price}</td>
            <td>
                <form action="rm_ticket_favorate.php" method="POST">
                    <input name="flight_id_1" value="{$data->flight_1_id}" hidden />
                    <input name="flight_id_2" value="" hidden />
                    <input name="flight_id_3" value="" hidden />
                    <button type=submit class="btn btn-success" >Remove</button>
                </form>
            </td>

        </tr>
__HTML__;
        
    }


    $sql = <<<__SQL__
    SELECT DISTINCT
    flight_1.id AS flight_1_id,
    flight_1.flight_number AS flight_1_flight_number,
    flight_1.departure AS flight_1_departure,
    flight_1.destination AS flight_1_destination,
    flight_1.departure_date AS flight_1_departure_date,
    flight_1.arrival_date AS flight_1_arrival_date,

    flight_2.id AS flight_2_id,
    flight_2.flight_number AS flight_2_flight_number,
    flight_2.departure AS flight_2_departure,
    flight_2.destination AS flight_2_destination,
    flight_2.departure_date AS flight_2_departure_date,
    flight_2.arrival_date AS flight_2_arrival_date,
    

    ((flight_1.ticket_price + flight_2.ticket_price) * 0.9)
        AS total_price

    FROM ticket_favorate
    JOIN flight AS flight_1 ON flight_1.id = ticket_favorate.flight_id_1
    JOIN flight AS flight_2 ON flight_2.id = ticket_favorate.flight_id_2
    WHERE ticket_favorate.user_id = ? AND ticket_favorate.flight_id_3 IS NULL
__SQL__;
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID));
    while($data = $sth->fetchObject()){
        echo <<<__HTML__
        <tr>
            <td>{$data->flight_1_flight_number}<br>{$data->flight_2_flight_number}</td>
            <td>{$data->flight_1_departure}<br>{$data->flight_2_departure}</td>
            <td>{$data->flight_1_destination}<br>{$data->flight_2_destination}</td>
            <td>{$data->flight_1_departure_date}<br>{$data->flight_2_departure_date}</td>
            <td>{$data->flight_1_arrival_date}<br>{$data->flight_2_arrival_date}</td>
            <td>{$data->total_price}</td>
            <td>
                <form action="rm_ticket_favorate.php" method="POST">
                    <input name="flight_id_1" value="{$data->flight_1_id}" hidden />
                    <input name="flight_id_2" value="{$data->flight_2_id}" hidden />
                    <input name="flight_id_3" value="" hidden />
                    <button type=submit class="btn btn-success" >Remove</button>
                </form>
            </td>
        </tr>
__HTML__;
        
    }



    $sql = <<<__SQL__
    SELECT DISTINCT
    flight_1.id AS flight_1_id,
    flight_1.flight_number AS flight_1_flight_number,
    flight_1.departure AS flight_1_departure,
    flight_1.destination AS flight_1_destination,
    flight_1.departure_date AS flight_1_departure_date,
    flight_1.arrival_date AS flight_1_arrival_date,

    flight_2.id AS flight_2_id,
    flight_2.flight_number AS flight_2_flight_number,
    flight_2.departure AS flight_2_departure,
    flight_2.destination AS flight_2_destination,
    flight_2.departure_date AS flight_2_departure_date,
    flight_2.arrival_date AS flight_2_arrival_date,
    
    flight_3.id AS flight_3_id,
    flight_3.flight_number AS flight_3_flight_number,
    flight_3.departure AS flight_3_departure,
    flight_3.destination AS flight_3_destination,
    flight_3.departure_date AS flight_3_departure_date,
    flight_3.arrival_date AS flight_3_arrival_date,

    case
      when flight_2.ticket_price IS NOT NULL AND flight_3.ticket_price IS NOT NULL then
        ((flight_1.ticket_price + flight_2.ticket_price + flight_3.ticket_price) * 0.8)
      when flight_2.ticket_price IS NOT NULL AND flight_3.ticket_price IS  NULL then
        ((flight_1.ticket_price + flight_2.ticket_price) * 0.9)
      else
        flight_1.ticket_price
    end AS total_price

    FROM ticket_favorate
    JOIN flight AS flight_1 ON flight_1.id = ticket_favorate.flight_id_1
    JOIN flight AS flight_2 ON flight_2.id = ticket_favorate.flight_id_2
    JOIN flight AS flight_3 ON flight_3.id = ticket_favorate.flight_id_3
    WHERE ticket_favorate.user_id = ?
__SQL__;
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($account_ID));
    while($data = $sth->fetchObject()){
        echo <<<__HTML__
        <tr>
            <td>{$data->flight_1_flight_number}<br>{$data->flight_2_flight_number}<br>{$data->flight_3_flight_number}</td>
            <td>{$data->flight_1_departure}<br>{$data->flight_2_departure}<br>{$data->flight_3_departure}</td>
            <td>{$data->flight_1_destination}<br>{$data->flight_2_destination}<br>{$data->flight_3_destination}</td>
            <td>{$data->flight_1_departure_date}<br>{$data->flight_2_departure_date}<br>{$data->flight_3_departure_date}</td>
            <td>{$data->flight_1_arrival_date}<br>{$data->flight_2_arrival_date}<br>{$data->flight_3_arrival_date}</td>
            <td>{$data->total_price}</td>
            <td>
                <form action="rm_ticket_favorate.php" method="POST">
                    <input name="flight_id_1" value="{$data->flight_1_id}" hidden />
                    <input name="flight_id_2" value="{$data->flight_2_id}" hidden />
                    <input name="flight_id_3" value="{$data->flight_3_id}" hidden />
                    <button type=submit class="btn btn-success" >Remove</button>
                </form>
            </td>
        </tr>
__HTML__;
        
    }
?>
        </table>

</body>
</html>
