<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');
    if(!$_SESSION['account']){
        header("Location: main.php");
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
<?php
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
    <title>Country Management</title>
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
    <h1>Country management</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <ul class="nav nav-tabs">
        <li><a href="main.php"><i class="icon-home"></i> Home</a></li>
        <li><a href="authority.php"><i class="icon-user"></i> User List</a></li>
        <li><a href="airport.php"><i class="icon-plane"></i> Airport List</a></li>
        <li class="active"><a href="country.php"><i class="icon-globe"></i> Country List</a></li>
        <li><a href="ticket_search.php"><i class="icon-ok-circle"></i> Ticket Search</a></li>
        <li><a href="compare.php"><i class="icon-heart"></i> Comparison Sheet</a></li>
    </ul>
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
        <td>Full Name</td>
        <td class="WideTd">Edit</td>
        <td class="WideTd">Delete</td>
        </tr>
<?php
    accessDB($db);
    if($db){
        $sql = "SELECT * "
                ."FROM `country` ";
        $sth = $db->prepare($sql);
        $result = $sth->execute();
    }
    echo "\n";
    while ($data = $sth->fetchObject()){
        echo "<tr>\n";
        #echo '<form action="edit_country.php" method="post">';
        echo "<td>";
        echo $data->id;
        echo "</td>\n";
        
        if($edit_id==$data->id){
            echo '<form action="edit_country.php" method="post">';
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
            echo '<input type="text" name="full_name" value="'.$data->full_name.'"></input>';
        }
        else{
            echo $data->full_name;
        }
        echo "</td>\n";
        
        echo "<td>";
        if($edit_id==$data->id){
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Comfirm</button>';
        }
        else{
            echo '<form action="country.php" method="post">';
            echo '<button class="btn btn-info" type="submit" name="edit_id" value='.$data->id.'>Edit</button>';
            echo '</form>';
        }
        if($edit_id==$data->id){
            echo '</form>';
        }
        echo "</td>\n";
        
        echo '<td>';
        echo '<form action="rm_country.php" method="post">';
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
        echo '<a class="btn btn-success" href="country.php" style="position: absolute;left: 90%;">Cancel</a>';
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
        <form action="add_country.php" method="POST">
        <table class="table-bordered table" width=1000 border=2 cellspacing=2>
            <br><h3>Add a new country</h3></br>
            <tr>
                <td>Name</td>
                <td>Full Name</td>
            </tr>
            <tr>
                <td><input type="text" name="name"></td>
                <td><input type="text" name="full_name"></td>
            </tr>
        </table>
        <br><button class="btn btn-success" name="insert" value=1  type="submit">Submit</button>
        <button class="btn btn-success" name="insert" value=0 type=submit>Cancel</button>
        </form>
__HTML__;
    }
    else{
        echo <<<__HTML__
        <form action="country.php" method="POST">
        <br><button class="btn btn-success" type="submit" name="insert" value="true">New</button></br>
        </form>
__HTML__;
    }
?>
</body>
</html>
