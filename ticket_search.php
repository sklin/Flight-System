<?php
    session_save_path('./sessions');
    session_start();
    #header("Location: login.php");
    include_once('config.php');
    include_once('user_sql_search.php');
    accessDB($db);
    if(!$_SESSION['account']){
        header("Location: index.php");
        exit();
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
<?php
    if($_POST['from']!="")
        $_SESSION['from'] = $_POST['from'];
    if($_POST['to']!="")
        $_SESSION['to'] = $_POST['to'];
    if($_POST['transfer-times']!="")
        $_SESSION['transfer-times'] = $_POST['transfer-times'];
?>
<?php
    if($_POST['order']!=""){
        $_SESSION['ticket_order'] = $_POST['order'];
    }
    if($_POST['order_method']!=""){
        $_SESSION['ticket_order_method'] = $_POST['order_method'];
    }
    if($_SESSION['ticket_order']!=""){
        $order = " ".$_SESSION['ticket_order'];
    }
    else{
        $order = " id";
    }
    if($_SESSION['ticket_order_method']!=""){
        $order_method = " ".$_SESSION['ticket_order_method'];
    }
    else{
        $order_method = " ASC";
    }
    if($_POST['Cancel']==1){
        unset($_SESSION['from']);
        unset($_SESSION['to']);
        unset($_SESSION['transfer-times']);
    }

    if($_SESSION['ticket_order']!=""){
        $order = " ".$_SESSION['ticket_order'];
    }
    else{
        $order = " price";
    }
    if($_SESSION['ticket_order_method']!=""){
        $order_method = " ".$_SESSION['ticket_order_method'];
    }
    else{
        $order_method = " ASC";
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
    <script src="jquery-2.1.1.js"></script>
    <script>
        $(document).ready(function(){
            $("#sql-flip").click(function(){
                $("#sql-toggle").fadeToggle();
            });
        });
    </script>

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
        .Error{
            font-size: 20px;
        }
        .input-block {
            padding-top: 10px;
            padding-left: 50px;
        }
        .Link{
            font-size: 20px;
            position: absolute;
            left: 90%;
            padding-right: 50px;
        }
        .Logout{
            font-size: 20px;
            position: absolute;
            left: 90%;
            padding-right: 50px;
        }

        #sql-toggle,#sql-flip
        {
        padding:5px;
        text-align:center;
        background-color:#e5eecc;
        border:solid 1px #c3c3c3;
        }
        #sql-toggle
        {
        padding:50px;
        display:none;
        }
        <?php
            css_inner_block();
        ?>
    </style>
</head>
<body>
    <h5 class="Logout"><a href="logout.php">logout</a></h5>
    <h1>Flight System</h1>
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
            <li class="active"><a href="ticket_search.php"><i class="icon-ok-circle"></i> Ticket Search</a></li>
        <li><a href="compare.php"><i class="icon-heart"></i> Comparison Sheet</a></li>
    </ul>
    <div class="input-block">
    <form action="ticket_search.php" method="POST">
        <h3 style="display:inline-block;">From :</h3>
        <select name="from">
            <option disabled selected>Choose an airport</option>
        <?php
            $sql = <<<__SQL__
SELECT 
    country.name AS country_name,
    country.full_name AS country_full_name,
    airport.name AS airport_name,
    airport.full_name AS airport_full_name
FROM country
JOIN airport ON airport.country_id =country.id
ORDER BY country_name ASC, airport_name ASC
__SQL__;
            $sth = $db->prepare($sql);
            $result = $sth->execute();
            $belong_country_name = "";
            $belong_country_fullname = "";
            while($data=$sth->fetchObject()){
                if($data->country_name!=$belong_country_name){
                    $belong_country_name = $data->country_name;
                    $belong_country_full_name = $data->country_full_name;
                    echo <<<__HTML__
                <optgroup label="---{$data->country_name},{$data->country_full_name}---">
__HTML__;
                    
                }
                echo <<<__HTML__
                <option value={$data->airport_name}>&nbsp;&nbsp;&nbsp;&nbsp;{$data->airport_name},{$data->airport_full_name}</option>
__HTML__;
            }
        ?>
        </select>

        <br></br>

        <h3 style="display:inline-block;">To :</h3>

        <select name="to">
            <option disabled selected>Choose an airport</option>
        <?php
            $sql = <<<__SQL__
SELECT 
    country.name AS country_name,
    country.full_name AS country_full_name,
    airport.name AS airport_name,
    airport.full_name AS airport_full_name
FROM country
JOIN airport ON airport.country_id =country.id
ORDER BY country_name ASC, airport_name ASC
__SQL__;
            $sth = $db->prepare($sql);
            $result = $sth->execute();
            $belong_country_name = "";
            $belong_country_fullname = "";
            while($data=$sth->fetchObject()){
                if($data->country_name!=$belong_country_name){
                    $belong_country_name = $data->country_name;
                    $belong_country_full_name = $data->country_full_name;
                    # Ori : <option disabled>---{$data->country_name},{$data->country_full_name}---</option>
                    echo <<<__HTML__
                <optgroup label="---{$data->country_name},{$data->country_full_name}---">
__HTML__;
                    
                }
                echo <<<__HTML__
                <option value={$data->airport_name}>&nbsp;&nbsp;&nbsp;&nbsp;{$data->airport_name},{$data->airport_full_name}</option>
__HTML__;
            }
        ?>
        </select>

        <br></br>

        <h3 style="display:inline-block;">Transfer times Limit : </h3>
        <select name="transfer-times" style="width : 70px;">
            <option>0</option>
            <option>1</option>
            <option>2</option>
        </select> times

        <br>
        <button type="submit" class="btn btn-primary">Search</button>
        <button type="submit" class="btn btn-success" name="Cancel" value=1>Cancel</button>
    </form>
    </div>
    <div class="display-block" style="width: 1350px;">
<?php
    if( $_POST['Cancel']!= 1 && $_SESSION['from']!="" && $_SESSION['to']!=""){
        echo <<<__HTML__
        <div id="order">
            <form method="POST" action="ticket_search.php">
                <h3>Order by :</h3>
                <select name="order">
                    <option value="price">Price</option>
                    <option value="departure_time">Departure Time</option>
                    <option value="arrival_time">Arrival Time</option>
                    <option value="flight_time">Flight Time</option>
__HTML__;

        if($_SESSION['transfer-times']==1 || $_SESSION['transfer-times']==2)
            echo <<<__HTML__
                    <option value="transfer_time">Transfer Time</option>
                    <option value="total_time">Total Time</option>
__HTML__;
        echo <<<__HTML__
                </select>
                <select name="order_method">
                    <option value="ASC">ASC</option>
                    <option value="DESC">DESC</option>
                </select>
                <button type="submit">Sort</button>
            </form>
        </div>
__HTML__;
        if($_SESSION['transfer-times']==0){
            $show_sql =  no_transfer($_SESSION['from'],$_SESSION['to'],$order,$order_method,$account_ID);
            echo <<<__HTML__
            <div id="sql-flip">Show SQL></div>
            <div id="sql-toggle">
                {$show_sql}
            </div>
__HTML__;
        }
        else if($_SESSION['transfer-times']==1){
            $show_sql = one_transfer($_SESSION['from'],$_SESSION['to'],$order,$order_method,$account_ID);
            echo <<<__HTML__
            <div id="sql-flip">Show SQL></div>
            <div id="sql-toggle">
                {$show_sql}
            </div>
__HTML__;
        }
        else if($_SESSION['transfer-times']==2){
            $show_sql = two_transfer($_SESSION['from'],$_SESSION['to'],$order,$order_method,$account_ID);
            echo <<<__HTML__
            <div id="sql-flip">Show SQL></div>
            <div id="sql-toggle">
                {$show_sql}
            </div>
__HTML__;
        }
    }
?>
    <?php   //Debug
        /*
        echo $_POST['from'];
        echo $_POST['to'];
        echo $_POST['transfer-times'];
        echo '<br></br>';
        echo $_SESSION['from'];
        echo $_SESSION['to'];
        echo $_SESSION['transfer-times'];
        echo '<br></br>';
        echo var_dump($_POST['from']);
        echo var_dump($_POST['to']);
        echo var_dump($_POST['transfer-times']);
        echo '<br></br>';
        echo var_dump($_SESSION['from']);
        echo var_dump($_SESSION['to']);
        echo var_dump($_SESSION['transfer-times']);
        */
    ?>
    </div>


    <script src="jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
        
</body>
</html>

