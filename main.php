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
<?php
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
    if($_POST['order']!=""){
        $_SESSION['main_order'] = $_POST['order'];
    }
    if($_POST['order_method']!=""){
        $_SESSION['main_order_method'] = $_POST['order_method'];
    }
    if($_POST['search']!=""){
        $_SESSION['main_search'] = $_POST['search'];
    }
    if($_POST['keyword']!=""){
        $_SESSION['main_keyword'] = $_POST['keyword'];
    }
    if($_POST['Clear']==1){
        unset($_SESSION['main_keyword']);
        unset($_SESSION['main_search']);    
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
        input{
            width: 120px;
        }
        
    </style>
</head>
<body>
    <h5 class="Logout"><a href="logout.php">logout</a></h5>
    <h1>Flight System</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <ul class="nav nav-pills">
        <li class="active"><a href="main.php">Home</a></li>
        <li><a href="authority.php">User List</a></li>
        <li><a href="airport.php">Airport List</a></li>
        <li><a href="compare.php">Comparison Sheet</a></li>
    </ul>
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
    <?php
        if($_SESSION['main_order']==="id"){
            echo '<option value="id" selected>ID</option>';
        }
        else{
            echo '<option value="id">ID</option>';
        }
        if($_SESSION['main_order']==="flight_number"){
            echo '<option value="flight_number" selected>Flight Number</option>';
        }
        else{
            echo '<option value="flight_number">Flight Number</option>';
        }
        if($_SESSION['main_order']==="departure"){
            echo '<option value="departure" selected>Departure</option>';
        }
        else{
            echo '<option value="departure">Departure</option>';
        }
        if($_SESSION['main_order']==="destination"){
            echo '<option value="destination" selected>Destination</option>';
        }
        else{
            echo '<option value="destination">Destination</option>';
        }
        if($_SESSION['main_order']==="departure_date"){
            echo '<option value="departure_date" selected>Departure Date</option>';
        }
        else{
            echo '<option value="departure_date">Departure Date</option>';
        }
        if($_SESSION['main_order']==="arrival_date"){
            echo '<option value="arrival_date" selected>Arrival Date</option>';
        }
        else{
            echo '<option value="arrival_date">Arrival Date</option>';
        }
        if($_SESSION['main_order']==="ticket_price"){
            echo '<option value="ticket_price" selected>Ticket Price</option>';
        }
        else{
            echo '<option value="ticket_price">Ticket Price</option>';
        }
    ?>
    </select>
    <select name="order_method">
    <?php
            if($_SESSION['main_order_method']==="ASC"){
                echo '<option value="ASC" selected>ASC</option>';
            }
            else{
                echo '<option value="ASC">ASC</option>';
            }
            if($_SESSION['main_order_method']==="DESC"){
                echo '<option value="DESC" selected>DESC</option>';
            }
            else{
                echo '<option value="DESC">DESC</option>';
            }
    ?>
    </select>
    <button type="submit">Sort</button>
    </form>
    <form method="POST" action="main.php">
    <select name="search">
        <?php
            if($_SESSION['main_search']==="id"){
                echo '<option value="id" selected>ID</option>';
            }
            else{
                echo '<option value="id">ID</option>';
            }
            if($_SESSION['main_search']==="flight_number"){
                echo '<option value="flight_number" selected>Flight Number</option>';
            }
            else{
                echo '<option value="flight_number">Flight Number</option>';
            }
            if($_SESSION['main_search']==="departure"){
                echo '<option value="departure" selected>Departure</option>';
            }
            else{
                echo '<option value="departure">Departure</option>';
            }
            if($_SESSION['main_search']==="destination"){
                echo '<option value="destination" selected>Destination</option>';
            }
            else{
                echo '<option value="destination">Destination</option>';
            }
            if($_SESSION['main_search']==="departure_date"){
                echo '<option value="departure_date" selected>Departure Date</option>';
            }
            else{
                echo '<option value="departure_date">Departure Date</option>';
            }
            if($_SESSION['main_search']==="arrival_date"){
                echo '<option value="arrival_date" selected>Arrival Date</option>';
            }
            else{
                echo '<option value="arrival_date">Arrival Date</option>';
            }
            if($_SESSION['main_search']==="ticket_price"){
                echo '<option value="ticket_price" selected>Ticket Price</option>';
            }
            else{
                echo '<option value="ticket_price">Ticket Price</option>';
            }
        ?>
    </select>
    <input type="text" name="keyword" value="<?php echo $_SESSION['main_keyword']; ?>"></input>
    <button type="submit" >Search</button>
    <button type="submit" name="Clear" value=1>Clear</button></br>
    </form>
    <table class="MainTable table table-hover table-condensed" width=1000 cellspacing=2 >
        <tr>
        <td>#</td>
        <td width=160>Flight Number</td>
        <td width=160>Departure</td>
        <td width=160>Destination</td>
        <td width=160>Departure Date</td>
        <td width=160>Arrival Date</td>
        <td width=160>Ticket Price</td>
        <td width=80 class="WideTd">Edit</td>
        <td width=80 class="WideTd">Delete</td>
        <td width=80 class="WideTd">Compare</td>
        </tr>
<?php
    if($_SESSION['main_order']!=""){
        $order = " ".$_SESSION['main_order'];
    }
    else{
        $order = " id";
    }
    if($_SESSION['main_order_method']!=""){
        $order_method = " ".$_SESSION['main_order_method'];
    }
    else{
        $order_method = " ASC";
    }
    if($_SESSION['main_keyword']!=""){
        $keyword = "%".$_SESSION['main_keyword']."%";
    }
    
    if($_SESSION['main_keyword']!=""){
        $sql = "SELECT * FROM `flight` "
                ."WHERE ". $_SESSION['main_search'] ." LIKE '" . $keyword . "' "
                ."ORDER BY " . $order . $order_method . ", flight_number ";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_SESSION['account_ID']));
    }
    else{
        $sql = "SELECT * FROM `flight` ORDER BY " . $order . $order_method . ", flight_number ";
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
        
        echo "<td width=160>";
        if($edit_id==$data->id){
            echo '<input type="text" name="flight_number" value="'.$data->flight_number.'"></input>';
        }
        else{
            echo $data->flight_number;
        }
        echo "</td>\n";

        echo "<td width=160>";
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

        echo "<td width=160>";
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

        echo "<td width=160>";
        if($edit_id==$data->id){
            echo '<input type="datetime-local" name="departure_date" value="'.$data->departure_date.'"></input>';
        }
        else{
            echo $data->departure_date;
        }
        echo "</td>\n";

        echo "<td width=160>";
        if($edit_id==$data->id){
            echo '<input type="datetime-local" name="arrival_date" value="'.$data->arrival_date.'"></input>';
        }
        else{
            echo $data->arrival_date;
        }
        echo "</td>\n";

        echo "<td width=160>";
        if($edit_id==$data->id){
            echo '<input type="text" name="ticket_price" value="'.$data->ticket_price.'"></input>';
        }
        else{
            echo $data->ticket_price;
        }
        echo "</td>\n";

        echo "<td width=80>";
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

        echo "<td width=80>";
        echo '<form action="delete.php" method="post">';
        echo '<button class="btn btn-danger" type="submit" name="Delete" value="'.$data->id.'">Delete</button>';
        echo '</form>';
        echo "</td>\n";

        echo "<td width=80>";
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
