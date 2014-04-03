<?php
session_save_path('./sessions');
session_start();
include_once('config.php');
    if(!$_SESSION['account']){
        header("Location: login.php");
        exit();
    }
    if(!$_SESSION['is_admin']){
        header("Location: user.php");
        exit();
    }
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
    $edit_id = $_POST['edit_id'];
    accessDB($db);
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
        .Error{
            font-size: 20px;
        }
        span {
            display:inline;
        }
        .WideTd{
            width: 80px;
        }
        
    </style>
</head>
<body>
    <h5 class="Logout"><a href="logout.php">logout</a></h5>
    <br><h5 class="Logout"><a href="authority.php">User list</a></h5>
    <br><h5 class="Logout"><a href="compare.php">Comparison sheet</a></h5>
    <h1>Flight System</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
<?php
            if($_SESSION['Edit_Error']){
                echo '<strong class="Error"><font color="#FF0000">'.$_SESSION['Edit_Error'].'</font></strong>';
                unset($_SESSION['Edit_Error']);

            }
?>
    <?php echo $_POST['order']; ?>
    <?php echo $_POST['order_method']; ?>
    <?php echo $_POST['edit_id']; ?>
    <form method="POST" action="main.php">
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
    <table class="MainTable table table-hover table-condensed" width=1000 cellspacing=2 >
        <tr>
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
        <td>Ticket Price</td>
        <td class="WideTd">Edit</td>
        <td class="WideTd">Delete</td>
        <td class="WideTd">Compare</td>
        </tr>
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
    if($db){
        $sql = "SELECT * FROM `flight` ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute();
    }
    echo "\n";
    while ($data = $sth->fetchObject()){
        echo "<tr>";

        if($edit_id==$data->id){
            echo '<form action="modify.php" method="post">';
        }

        echo "<td>";
        echo $data->id;
        echo "</td>\n";
        
        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="text" name="flight_number" value="'.$data->flight_number.'"></input>';
        }
        else{
            echo $data->flight_number;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<select name="departure">';
            $sql3 = "SELECT `name` FROM `airport` ";
            $sth3 = $db->prepare($sql3);
            $result3 = $sth3->execute();
            while ($data3 = $sth3->fetchObject()){
                if($data3->name===$data->departure){
                    echo '<option selected>'.$data3->name.'</option>';
                }
                else{
                    echo '<option>'.$data3->name.'</option>';
                }
            }
            echo '</select>';
        }
        else{
            echo $data->departure;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<select name="destination">';
            $sql3 = "SELECT `name` FROM `airport` ";
            $sth3 = $db->prepare($sql3);
            $result3 = $sth3->execute();
            while ($data3 = $sth3->fetchObject()){
                if($data3->name===$data->destination){
                    echo '<option selected>'.$data3->name.'</option>';
                }
                else{
                    echo '<option>'.$data3->name.'</option>';
                }
            }
            echo '</select>';
        }
        else{
            echo $data->destination;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="datetime-local" name="departure_date" value="'.$data->departure_date.'"></input>';
        }
        else{
            echo $data->departure_date;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="datetime-local" name="arrival_date" value="'.$data->arrival_date.'"></input>';
        }
        else{
            echo $data->arrival_date;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="text" name="ticket_price" value="'.$data->ticket_price.'"></input>';
        }
        else{
            echo $data->ticket_price;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Comfirm</button>';
        }
        else{
            echo '<form action="main.php" method="post">';
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Edit</button>';
            echo '</form>';
        }
        if($edit_id==$data->id){
            echo '</form>';
        }
        echo "</td>\n";

        echo '<td>';
        echo '<form action="delete.php" method="post">';
        echo '<button class="btn btn-danger" type="submit" name="Delete" value="'.$data->id.'">Delete</button>';
        echo '</form>';
        echo "</td>\n";

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
        
        echo "</tr>\n";
    }
    echo "</table>";
?>
<?php
    if($_POST['edit_id']){
        echo '<a class="btn btn-success" href="main.php" style="position: absolute;left: 90%;">Cancel</a>';
    }
?>
<?php
    # Insert form
    if($_SESSION['Insert_Error']){
        echo '<strong class="Error"><font color="#FF0000">'.$_SESSION['Insert_Error'].'</font></strong>';
        unset($_SESSION['Insert_Error']);

    }
    if($_POST['insert']){
        echo <<<__HTML__
        <form action="insert.php" method="POST">
        <table class="table-bordered table" width=1000 border=2 cellspacing=2>
            <br><h3>新增一筆資料</h3></br>
            <tr>
                <td>Flight Number</td>
                <td>Departure</td>
                <td>Destination</td>
                <td>Departure Date</td>
                <td>Arrival Date</td>
                <td>Ticket price</td>
            </tr>
            <tr>
                <td><input type="text" name="flight_number"></td>
                <td>
                    <select name="departure">
__HTML__;
        $sql = "SELECT `name` FROM `airport` ";
        $sth = $db->prepare($sql);
        $result = $sth->execute();
        while ($data = $sth->fetchObject()){
            echo '<option>'.$data->name.'</option>';
        }
        echo <<<__HTML__
                    </select>
                </td>
                <td>
                    <select name="destination">
__HTML__;
        $sql = "SELECT `name` FROM `airport` ";
        $sth = $db->prepare($sql);
        $result = $sth->execute();
        while ($data = $sth->fetchObject()){
            echo '<option>'.$data->name.'</option>';
        }
        echo <<<__HTML__
                </td>
                <td><input type="datetime-local" name="departure_date"></input></td>
                <td><input type="datetime-local" name="arrival_date"></input></td>
                <td><input type="text" name="ticket_price"></td>
                    </select>
            </tr>
        </table>
        <br><button class="btn btn-success" name="insert" value=1  type="submit">Submit</button>
        <button class="btn btn-success" name="insert" value=0 type=submit>Cancel</button>
        </form>
__HTML__;
    }
    else{
        echo <<<__HTML__
        <form action="main.php" method="POST">
        <br><button class="btn btn-success" type="submit" name="insert" value="true">New</button></br>
        </form>
__HTML__;
    }
?>
</body>
</html>
