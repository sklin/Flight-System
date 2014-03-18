<?php

include_once('config.php');
#show_main_page();
#show_error_page();
#hello();

    try
    {
        $dsn = "mysql:host=$db_host;dbname=$db_name";
        $db = new PDO($dsn,$db_user,$db_password);
        echo "<br>in try</br>";
        echo "<br>$db_name</br>";
    }
    catch (PDOException $ex)
    {
        $err_msg = $ex->getMessage();
        echo "<br>in catch</br>";
    }
    if($db)
    {
        echo "<br>open DB success!</br>";

        $account = "test3";
        $passwd = "passwd";
        $is_admin = 1;

        $sql = "INSERT INTO `user` (account,password,is_admin)"
             . " VALUES(?, ?, ?)";
        $sth = $db->prepare($sql);
        if($sth)
        {
            echo "<br>Prepare success!</br>";
        }
        else
        {
            echo "<br>Prepare fail!</br>";
        }
        $result = $sth->execute(array($account,$passwd,$is_admin));
        if($result)
        {
            echo "<br>Execute success!</br>";
        }
        else
        {
            echo "<br>Execute fail!</br>";
            print_r( $sth->errorInfo());
        }

    }
    else
    {
        echo "<br>open DB fail!</br>";
    }


?>
