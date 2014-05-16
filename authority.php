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
    accessDB($db);
?>
<?php
    $account = $_SESSION['account'];
    $account_ID = $_SESSION['account_ID'];
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
    if($_POST['delete']){
        $sql = "DELETE FROM `user` "
             . "WHERE id = ?";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array($_POST['delete']));
    }
    if($_POST['change']){
        $sql = "UPDATE `user` "
             . "SET `is_admin` = ? "
             . "WHERE `user`.`id` = ?";
        $sth = $db->prepare($sql);
        $result = $sth->execute(array(1,$_POST['change']));
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Authority</title>
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
    <h1>Admin Authority</h1>
    <h3>Hello, <?php echo $_SESSION['account']; ?></h3>
    <ul class="nav nav-tabs">
        <li><a href="main.php"><i class="icon-home"></i> Home</a></li>
        <li class="active"><a href="authority.php"><i class="icon-user"></i> User List</a></li>
        <li><a href="airport.php"><i class="icon-plane"></i> Airport List</a></li>
        <li><a href="country.php"><i class="icon-globe"></i> Country List</a></li>
        <li><a href="compare.php"><i class="icon-heart"></i> Comparison Sheet</a></li>
    </ul>
    <strong><font color="#FF0000"><?php echo $_SESSION['Error']; ?></font></strong>
<?php
    unset($_SESSION['Error']);
    if($_POST['add']){
        echo <<<__HTML__
        <form class="form-signin" action="adduser.php" method="POST">
            <h4>Account : <input type="text" name="account"></h4>
            <h4>Password : <input type="password" name="password"></h4>
            <br><input type="checkbox" name="is_admin" value=1> Is Admin ?</input></br>
            <br><button class="btn btn-primary" name="add" value=1>Confirm</button>
            <button class="btn btn-primary" >Cancel</button></br>
        </form>
__HTML__;
    }
    else{
        echo <<<__HTML__
    <form action="authority.php" method="POST">
        <button class="btn btn-primary" name="add" value=1>Add new user</button>
    </form>
__HTML__;
    }
?>
    <table class="MainTable table-bordered table table-hover table-condensed" width=1000 border=2 cellspacing=2 >
        <tr>
        <td>#</td>
        <td>Account</td>
        <td>Identity</td>
        <td>Modify</td>
        <td>Delete</td>
        </tr>
            
<?php
$sql = "SELECT * FROM `user` ORDER BY id";
$sth = $db->prepare($sql);
$result = $sth->execute();
/*
if(!$result){
    header("Location: main.php");
    exit();
}
*/
while ($data = $sth->fetchObject()){
        echo "<tr>";
        echo "<td>".$data->id."</td>"."";
        echo "<td>".$data->account."</td>";
        if($data->is_admin==1){
            echo "<td>Administrater</td>";
        }
        else{
            echo "<td>User</td>";
        }
        echo "<td>";
        if($data->is_admin==1){
        }
        else{
            echo '<form action="authority.php" method="post">';
            echo '<button class="btn btn-info" name="change" value='.$data->id.'>change to Admin</button>';
            echo '</form>';
        }
        echo '</td>';
        echo '<td>';
        echo '<form action="authority.php" method="post">';
        echo '<button class="btn btn-danger" name="delete" value='.$data->id.'>Delete</button>';
        echo '</form>';
        echo "</td>";
        echo "</tr>"."\n";
}
?>
    </table>
</body>
</html>
