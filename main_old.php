<?php
    session_save_path('./sessions');
    session_start();
    include_once('config.php');

    print "<br>Hello ${_SESSION['account']}</br>";
    print $_POST['account'];




    try
    {
        $dsn = "mysql:host=$db_host;dbname=$db_name";
        $db = new PDO($dsn,$db_user,$db_password);
    }
    catch (PDOException $ex)
    {
        $err_msg = $ex->getMessage();
    }
    if($db)
    {
        $sql = "SELECT * FROM `user`";
        $sth = $db->prepare($sql);
        if($sth)
        {
            echo "<br>Prepare success!</br>";
        }
        else
        {
            echo "<br>Prepare fail!</br>";
        }
        $result = $sth->execute();
        if($result)
        {
            echo "<br>Execute success!</br>";
            #header("Location: login.php");
            
            if($sth->fetchObject())
            {
                print "Y";
            }
            else
            {
                print "N";
            }
            while ($re = $sth->fetchObject())
            {
                echo "<br>".$re->account .' '. $re->password."</br>";
            }





        }
        else
        {
            echo "<br>Execute fail!</br>";
            print_r( $sth->errorInfo());
            #header("Location: error.php");
        }

    }
    else
    {
        echo "<br>open DB fail!</br>";
        #header("Location: error.php");
    }
?>

<h3><a href="logout.php">logout</a></h3>
