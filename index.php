<?php
    #header("Location: login.php");
    include_once('config.php');
    include_once('sql_search.php');
    accessDB($db);
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
        .Error{
            font-size: 20px;
        }
        .input-block {
            padding-top: 10px;
            padding-left: 50px;
        }
    </style>
</head>
<body>
    <h1>Flight System</h1>
    <div class="input-block">
    <form action="index.php" method="POST">
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
        <a class="btn btn-success" href="index.php">Cancel</a>
    </form>
    </div>
    <div class="display-block">
<?php
    if($_POST && $_POST['from']!="" && $_POST['to']!=""){
        if($_POST['transfer-times']==0)
            no_transfer($_POST['from'],$_POST['to']);
        else if($_POST['transfer-times']==1)
            one_transfer($_POST['from'],$_POST['to']);
        else if($_POST['transfer-times']==2)
            two_transfer($_POST['from'],$_POST['to']);
    }
?>
    </div>

    <script src="jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
        
</body>
</html>
