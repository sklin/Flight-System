<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    
    if(!$_SESSION['account']){
        header("Location: main.php");
        exit();
    }
    if(!$_SESSION['is_admin']){
        header("Location: main.php");
        exit();
    }
    if(count($_POST)==0){
        header("Location: main.php");
        exit();
    }


    accessDB($db);
    $sql = "SELECT * FROM `flight` WHERE `id` = ?";
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($_POST['Edit']));
    if(!$result){
        header("Location: main.php");
        exit();
    }
    $data = $sth->fetchObject();
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
    <h1>Edit</h1>

    <form action="modify.php" method="POST">
        <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
            <tr>
            <td>#</td>
            <td>Flight Number</td>
            <td>Departure</td>
            <td>Destination</td>
            <td>Departure Date</td>
            <td>Arrival Date</td>
            </tr>
            
            <tr>
            <td><?php echo $data->id; ?></td>
            <td><input type="text" name="flight_number" value=<?php echo $data->flight_number; ?>></td>
            <td><input type="text" name="departure" value=<?php echo $data->departure; ?>></td>
            <td><input type="text" name="destination" value=<?php echo $data->destination; ?>></td>
            <td><input type="datetime-local" name="departure_date" value=<?php echo $data->departure_date; ?>></td>
            <td><input type="datetime-local" name="arrival_date" value=<?php echo $data->arrival_date; ?>></td>
        </table>
        <input type="hidden" name="id" value=<?php echo $_POST['Edit']; ?>>
        <br><button class="btn btn-success" type="submit" name="modify" value=1>Confirm</button>
        <button class="btn btn-success" type="submit" name="modify" value=0>Cancel</button>
    </form>
</body>
</html>
