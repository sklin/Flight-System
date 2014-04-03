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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Airport Management</title>
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
    <br><h5 class="Logout"><a href="main.php">Back</a></h5>
    <h1>Airport management</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
<?php
            if($_SESSION['Error']){
                echo '<strong class="Error"><font color="#FF0000">'.$_SESSION['Error'].'</font></strong>';
                unset($_SESSION['Error']);

            }
?>
    <?php echo $_POST['order']; ?>
    <?php echo $_POST['order_method']; ?>
    <form method="POST" action="airport.php">
    <select name="order">
        <option value="id">ID</option>
        <option value="name">Name</option>
        <option value="longitude">Longitude</option>
        <option value="latitude">Latitude</option>
    </select>
    <select name="order_method">
        <option value="ASC" selected>ASC</option>
        <option value="DESC">DESC</option>
    </select>
    <button type="submit">Sort</button></br>
    </form>
<?php
    if($_SESSION['Edit_Error']){
        echo '<strong class="Error"><font color="#FF0000">'.$_SESSION['Edit_Error'].'</font></strong>';
        unset($_SESSION['Edit_Error']);

    }
?>
    <table class="MainTable table table-hover table-condensed" width=1000 cellspacing=2 >
        <tr>
        <td>#</td>
        <td>Name</td>
        <td>Longitude</td>
        <td>Latitude</td>
        <td class="WideTd">Edit</td>
        <td class="WideTd">Delete</td>
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
    accessDB($db);
    if($db){
        $sql = "SELECT * FROM `airport` ORDER BY " . $order . $order_method;
        $sth = $db->prepare($sql);
        $result = $sth->execute();
    }
    echo "\n";
    while ($data = $sth->fetchObject()){
        echo "<tr>\n";
        #echo '<form action="edit_airport.php" method="post">';
        echo "<td>";
        echo $data->id;
        echo "</td>\n";
        
        if($edit_id==$data->id){
            echo '<form action="edit_airport.php" method="post">';
        }
        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="text" name="name" value="'.$data->name.'"></input>';
        }
        else{
            echo $data->name;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="text" name="longitude" value="'.$data->longitude.'"></input>';
        }
        else{
            echo $data->longitude;
        }
        echo "</td>\n";
        
        echo "<td>";
        if($edit_id==$data->id){
            echo '<input type="text" name="latitude" value="'.$data->latitude.'"></input>';
        }
        else{
            echo $data->latitude;
        }
        echo "</td>\n";

        echo "<td>";
        if($edit_id==$data->id){
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Comfirm</button>';
        }
        else{
            echo '<form action="airport.php" method="post">';
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Edit</button>';
            echo '</form>';
        }
        if($edit_id==$data->id){
            echo '</form>';
        }
        echo "</td>\n";
        
        echo '<td>';
        echo '<form action="rm_airport.php" method="post">';
        echo '<button class="btn btn-danger" type="submit" name="delete" value="'.$data->id.'">Delete</button>';
        echo '</form>';
        echo "</td>\n";
        
        echo "</tr>"."\n";
        #echo '</form>';
    }
    echo "</table>";
?>
<?php
    if($_POST['edit_id']){
        echo '<a class="btn btn-success" href="airport.php" style="position: absolute;left: 90%;">Cancel</a>';
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
        <form action="add_airport.php" method="POST">
        <table class="table-bordered table" width=1000 border=2 cellspacing=2>
            <br><h3>Add a new airport</h3></br>
            <tr>
                <td>Name</td>
                <td>Longitude</td>
                <td>Latitude</td>
            </tr>
            <tr>
                <td><input type="text" name="name"></td>
                <td><input type="number" name="longitude" step=0.000001 placeholder="Longitude"></input></td>
                <td><input type="number" name="latitude" step=0.000001 placeholder="Latitude"></input></td>
            </tr>
        </table>
        <br><button class="btn btn-success" name="insert" value=1  type="submit">Submit</button>
        <button class="btn btn-success" name="insert" value=0 type=submit>Cancel</button>
        </form>
__HTML__;
    }
    else{
        echo <<<__HTML__
        <form action="airport.php" method="POST">
        <br><button class="btn btn-success" type="submit" name="insert" value="true">New</button></br>
        </form>
__HTML__;
    }
?>
</body>
</html>
