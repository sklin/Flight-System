<?php

session_save_path('./sessions');
session_start();
include_once('config.php');

if(!$_SESSION['account']){
    header("Location: login.php");
}
else{
    # For Admin
    if($_SESSION['is_admin']){
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
            header("Location: error.php");
            exit();
        }
        if($db){
            $sql = "SELECT * FROM `flight`";
            $sth = $db->prepare($sql);
            $result = $sth->execute();
            echo <<<__HTML__
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
    <h1>Flight System</h1>
__HTML__;
            if($_SESSION['Edit_Error']){
                echo '<strong class="Error"><font color="#FF0000">'.$_SESSION['Edit_Error'].'</font></strong>';
                unset($_SESSION['Edit_Error']);

            }
            echo <<<__HTML__
    <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
        <tr>
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
        <td class="WideTd">Edit</td>
        <td class="WideTd">Delete</td>
        </tr>
__HTML__;
            echo "\n";
            while ($data = $sth->fetchObject()){
                echo "<tr>";
                echo "<td>".$data->id."</td>"."";
                echo "<td>".$data->flight_number."</td>";
                echo "<td>".$data->departure."</td>";
                echo "<td>".$data->destination."</td>";
                echo "<td>".$data->departure_date."</td>";
                echo "<td>".$data->arrival_date."</td>";

                echo "<td>";
                echo '<form action="edit.php" method="post">';
                echo '<button class="btn btn-info" type="submit" name="Edit" value="'.$data->id.'"> Edit </button>';
                echo '</form>';
                echo '</td>';
                echo '<td>';
                echo '<form action="delete.php" method="post">';
                echo '<button class="btn btn-danger" type="submit" name="Delete" value="'.$data->id.'">Delete</button>';
                echo '</form>';
                echo "</td>";
                echo "</tr>"."\n";
            }
            echo "</table>";
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
            </tr>
            <tr>
                <td><input type="text" name="flight_number"></td>
                <td><input type="text" name="departure"></td>
                <td><input type="text" name="destination"></td>
                <td><input type="datetime-local" name="departure_date"></td>
                <td><input type="datetime-local" name="arrival_date"></td>
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
            echo <<<__HTML__
</body>
</html>
__HTML__;
        } # End of if($db)

    } # End of Admin-if


    # For Normal User
    else{
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
            header("Location: error.php");
            exit();
        }
        if($db){
            $sql = "SELECT * FROM `flight`";
            $sth = $db->prepare($sql);
            $result = $sth->execute();
            echo <<<__HTML__
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
    <table class="MainTable table-bordered table table-hover table-condensed" width=800 border=2 cellspacing=2 >
        <td>#</td>
        <td>Flight Number</td>
        <td>Departure</td>
        <td>Destination</td>
        <td>Departure Date</td>
        <td>Arrival Date</td>
__HTML__;
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
            echo <<<__HTML__
</body>
</html>
__HTML__;
        } # End of if($db)

    } # End of Normal User
}



?>
