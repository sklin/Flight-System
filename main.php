<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');

    if(!$_SESSION['account'])
    {
        header("Location: login.php");
        exit();
    }
    else
    {

        echo "<!doctype html>"."\n";
        echo '<html lang="en">'."\n";
        echo "<head>"."\n";
        echo '<meta charset="utf-8">'."\n";
        echo "<title>Welcome to flight schedule system!</title>"."\n";
        echo "</head>"."\n";
        echo "<body>"."\n";
        echo '<h5><a href="logout.php">logout</a></h5>'."\n";
        echo "<h1>Main page</h1>"."\n";
        try{
            $dsn = "mysql:host=$db_host;dbname=$db_name";
            $db = new PDO($dsn,$db_user,$db_password);
        }catch (PDOException $ex){
            $err_msg = $ex->getMessage();
        }
        if($db){
            $sql = "SELECT * FROM `flight`";
            $sth = $db->prepare($sql);
            $result = $sth->execute();
            if($result){
                #echo "<br>Execute success!</br>";
                #if($sth->fetchObject()){ print "Y"; }
                #else{ print "N"; }
                echo "<table width=800 border=2 cellspacing=2 >\n";
                echo "<td>#</td>";
                echo "<td>Flight Number</td>";
                echo "<td>Departure</td>";
                echo "<td>Destination</td>";
                echo "<td>Departure Date</td>";
                echo "<td>Arrival Date</td>\n";
                #print "<br>".$sth->fetchObject()->id."</br>";
                while ($data = $sth->fetchObject()){
                    echo "<tr>";
                    echo "<td>".$data->id."</td>";
                    #echo "<td>".$data->account."</td>";
                    echo "<td>".$data->flight_number."</td>";
                    echo "<td>".$data->departure."</td>";
                    echo "<td>".$data->destination."</td>";
                    echo "<td>".$data->departure_date."</td>";
                    echo "<td>".$data->arrival_date."</td>";
                    echo "</tr>\n";
                }
                echo "</table>"."\n";
                if($_SESSION['is_admin']){
#                    if($_SESSION['insert']){
                        echo '<br><h3>新增一筆資料</h3></br>';
                        echo '<form action="insert.php" method="POST">';
                        echo '<br>Flight Number : <input type="text" name="flight_number"></br>';
                        echo '<br>Departure : <input type="text" name="departure"></br>';
                        echo '<br>Destination : <input type="text" name="destination"></br>';
                        echo '<br>Departure Date : <input type="text" name="departure_date"></br>';
                        echo '<br>Arrival Date : <input type="text" name="arrival_date"></br>';
                        echo '<br><button type="submit">Submit</button></br>';
                        echo '</form>';
#                        unset($_SESSION['insert']);
#                    }
#                    else{
#                        echo '<br><botton type="submit" action="main.php">New</botton></br>';
#                        $_SESSION['insert'] = 1;
#                    }
                }
                echo "</body>"."\n";
                echo "</html>"."\n";
            }else{
                #echo "<br>Execute fail!</br>";
                print_r( $sth->errorInfo());
                #header("Location: error.php");
            }

        }else{
            echo "<br>open DB fail!</br>";
            #header("Location: error.php");
        }
    }
?>

<?php

function show_main_page2()
{
    echo <<<MAIN_HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to flight schedule system!</title>
</head>
<body>
    <h5><a href="logout.php">logout</a></h5>
    <h1>Main page</h1>
    <table width=800 border=2 cellspacing=2 >
        <td>#</td><td>Flight Number</td><td>Departure</td><td>Destination</td><td>Departure Date</td><td>Arrival Date</td>
        <tr><td>#</td><td>FlightNum</td><td>Departure</td><td>Destination</td><td>Departure Date</td><td>Arrival Date</td></tr>
    </table>
        <?php echo "haha"; ?>
</body>
</html>


MAIN_HTML;
}
?>
