<?php
include_once('config.php');

function no_transfer($depart,$dest,$order,$method)
{
    accessDB($db);
    $sql = <<<__SQL__
SELECT
    flight.flight_number,
    flight.departure,
    flight.destination,
    subtime( addtime( flight.departure_date, depart.timezone ) , "12:00:00" )
        AS departure_time,
    subtime( addtime( flight.arrival_date, dest.timezone ) , "12:00:00" )
        AS arrival_time,
    TIMEDIFF( flight.arrival_date, flight.departure_date) AS flight_time,
    flight.ticket_price AS price
FROM flight
JOIN airport AS depart ON flight.departure = depart.name
JOIN airport AS dest ON flight.destination = dest.name
WHERE flight.departure = ?
  AND flight.destination = ?
__SQL__;
    $sql .= " ORDER BY " . $order . $order_method;
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($depart,$dest));
    #echo var_dump($sth);
    #echo var_dump($result);
    #echo var_dump($sth->fetchObject());
    echo <<<__HTML__
    <table class="table table-hover table-condensed">
    <tr>
    <td>Result</td>
    <td>Flight Number</td>
    <td>Departure Airport</td>
    <td>Destination Airport</td>
    <td>Departure Time</td>
    <td>Arrival Time</td>
    <td>Flight Time</td>
    <td>Price</td>
    </tr>
__HTML__;
    $count = 0;
    while($data=$sth->fetchObject()){
        echo '<tr>';
        echo '<td>' . ++$count . '</td>';
        echo '<td>' . $data->flight_number . '</td>';
        echo '<td>' . $data->departure . '</td>';
        echo '<td>' . $data->destination . '</td>';
        echo '<td>' . $data->departure_time . '</td>';
        echo '<td>' . $data->arrival_time . '</td>';
        echo '<td>' . $data->flight_time . '</td>';
        echo '<td>' . $data->price . '</td>';
        echo '</tr>';
    }
    echo <<<__HTML__
    </table>
__HTML__;
}

function one_transfer($depart,$dest,$order,$order_method)
{
    accessDB($db);
    $sql =<<<__SQL__
SELECT

TIMEDIFF(
  case
    when one_second.flight_number IS NOT NULL
        then one_second.arrival_date
    else
        one_first.arrival_date
  end,
  one_first.departure_date
)
AS total_time,

one_first.departure_date AS departure_time,

case
  when one_second.flight_number IS NOT NULL
      then one_second.arrival_date
  else
      one_first.arrival_date
end AS arrival_time,

SUBTIME( ADDTIME(one_first.departure_date, one_first.depart_timezone), "12:00:00") AS one_first_departure_time,
SUBTIME( ADDTIME(one_first.arrival_date, one_first.dest_timezone), "12:00:00") AS one_first_arrival_time,
SUBTIME( ADDTIME(one_second.departure_date, one_second.depart_timezone), "12:00:00") AS one_second_departure_time,
SUBTIME( ADDTIME(one_second.arrival_date, one_second.dest_timezone), "12:00:00") AS one_second_arrival_time,

TIMEDIFF( one_first.arrival_date, one_first.departure_date) AS flight_time_1,
TIMEDIFF( one_second.arrival_date, one_second.departure_date) AS flight_time_2,

case
  when TIMEDIFF( one_second.arrival_date, one_second.departure_date) IS NOT NULL
      then
            ADDTIME( TIMEDIFF( one_first.arrival_date, one_first.departure_date)
                    , TIMEDIFF( one_second.arrival_date, one_second.departure_date)) 
      else
            TIMEDIFF( one_first.arrival_date, one_first.departure_date)
end AS flight_time,

case
  when TIMEDIFF( one_second.departure_date, one_first.arrival_date) IS NOT NULL
      then
            TIMEDIFF( one_second.departure_date, one_first.arrival_date)
      else
            "00:00:00"
end AS transfer_time,

case
  when one_second.flight_number IS NOT NULL
      then
            round((one_first.ticket_price + one_second.ticket_price) * 0.9)
      else
            one_first.ticket_price
end AS price,

one_first.id AS one_first_id,
one_first.flight_number AS one_first_flight_number,
one_first.departure AS one_first_departure,
one_first.destination AS one_first_destination,
one_first.departure_date AS one_first_departure_date,
one_first.arrival_date AS one_first_arrival_date,
one_first.depart_timezone AS one_first_depart_timezone,
one_first.dest_timezone AS one_first_dest_timezone,
one_first.ticket_price AS one_first_ticket_price,

one_second.id AS one_second_id,
one_second.flight_number AS one_second_flight_number,
one_second.departure AS one_second_departure,
one_second.destination AS one_second_destination,
one_second.departure_date AS one_second_departure_date,
one_second.arrival_date AS one_second_arrival_date,
one_second.depart_timezone AS one_second_depart_timezone,
one_second.dest_timezone AS one_second_dest_timezone,
one_second.ticket_price AS one_second_ticket_price
FROM
(
  SELECT flight.id AS id,
    flight.flight_number AS flight_number,
    flight.departure AS departure,
    flight.destination AS destination,
    flight.departure_date AS departure_date,
    flight.arrival_date AS arrival_date,
    flight.ticket_price AS ticket_price,
    A.timezone AS depart_timezone,
    B.timezone AS dest_timezone
    FROM flight
    JOIN airport AS A ON flight.departure = A.name
    JOIN airport AS B ON flight.destination = B.name
) AS one_first
,
(
  (
    SELECT 
    flight.id AS id,
    flight.flight_number AS flight_number,
    flight.departure AS departure,
    flight.destination AS destination,
    flight.departure_date AS departure_date,
    flight.arrival_date AS arrival_date,
    flight.ticket_price AS ticket_price,
    A.timezone AS depart_timezone,
    B.timezone AS dest_timezone
    FROM flight
    JOIN airport AS A ON flight.departure = A.name
    JOIN airport AS B ON flight.destination = B.name
  )
  UNION
  (
    SELECT NULL AS id,
    NULL AS flight_number,
    NULL AS departure,
    NULL AS destination,
    NULL AS departure_date,
    NULL AS arrival_date,
    NULL AS depart_timezone,
    NULL AS dest_timezone,
    0 AS ticket_price
  )
) AS one_second

WHERE one_first.departure = ?
AND (
(one_second.departure IS NULL AND one_first.destination = ?)
OR
(one_first.destination = one_second.departure AND one_second.destination = ? AND ADDTIME(one_first.arrival_date,"02:00:00") < one_second.departure_date)
)
__SQL__;
    $sql .= " ORDER BY " . $order . $order_method;
    //$depart,$dest,$dest
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($depart,$dest,$dest));
    echo <<<__HTML__
        <table class="table table-hover table-condensed">
            <tr>
                <td>Result</td>
                <td>Flight Number</td>
                <td>Departure Airport</td>
                <td>Destination Airport</td>
                <td>Departure Time</td>
                <td>Arrival Time</td>
                <td>Flight Time</td>
                <td>Transfer Time</td>
                <td>Total Time</td>
                <td>Price</td>
            </tr>
__HTML__;

    $counter = 0;
    while($data=$sth->fetchObject()){
        if($data->one_second_flight_number !== NULL){
            echo '<tr>';
            echo '<td rowspan=2>' . ++$counter . '</td>';
            echo '<td>' . $data->one_first_flight_number . '</td>';
            echo '<td>' . $data->one_first_departure . '</td>';
            echo '<td>' . $data->one_first_destination . '</td>';
            echo '<td>' . $data->one_first_departure_time . '</td>';
            echo '<td>' . $data->one_first_arrival_time . '</td>';
            echo '<td rowspan=2>' . $data->flight_time . '</td>';
            echo '<td rowspan=2>' . $data->transfer_time . '</td>';
            echo '<td rowspan=2>' . $data->total_time . '</td>';
            echo '<td rowspan=2>' . $data->price . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td>' . $data->one_second_flight_number . '</td>';
            echo '<td>' . $data->one_second_departure . '</td>';
            echo '<td>' . $data->one_second_destination . '</td>';
            echo '<td>' . $data->one_second_departure_time . '</td>';
            echo '<td>' . $data->one_second_arrival_time . '</td>';
            echo '</tr>';
        }
        else{
            echo '<tr>';
            echo '<td>' . ++$counter . '</td>';
            echo '<td>' . $data->one_first_flight_number . '</td>';
            echo '<td>' . $data->one_first_departure . '</td>';
            echo '<td>' . $data->one_first_destination . '</td>';
            echo '<td>' . $data->one_first_departure_time . '</td>';
            echo '<td>' . $data->one_first_arrival_time . '</td>';
            echo '<td>' . $data->flight_time . '</td>';
            echo '<td>' . $data->transfer_time . '</td>';
            echo '<td>' . $data->total_time . '</td>';
            echo '<td>' . $data->price . '</td>';
            echo '</tr>';
        }

    }
    echo <<<__HTML__
    </table>
__HTML__;
}

function two_transfer($depart,$dest,$order,$order_method)
{
    accessDB($db);
    $sql = <<<__SQL__
SELECT
TIMEDIFF(
  case
    when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
        then two_third.arrival_date
    when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
        then two_second.arrival_date
    else
             two_first.arrival_date
  end,
  two_first.departure_date
) AS total_time,

two_first.departure_date AS departure_time,

case
  when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
    then two_third.arrival_date
  when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
    then two_second.arrival_date
  else
        two_first.arrival_date
end AS arrival_time,

SUBTIME( ADDTIME(two_first.departure_date, two_first.depart_timezone), "12:00:00") AS two_first_departure_time,
SUBTIME( ADDTIME(two_first.arrival_date, two_first.dest_timezone), "12:00:00") AS two_first_arrival_time,
SUBTIME( ADDTIME(two_second.departure_date, two_second.depart_timezone), "12:00:00") AS two_second_departure_time,
SUBTIME( ADDTIME(two_second.arrival_date, two_second.dest_timezone), "12:00:00") AS two_second_arrival_time,
SUBTIME( ADDTIME(two_third.departure_date, two_third.depart_timezone), "12:00:00") AS two_third_departure_time,
SUBTIME( ADDTIME(two_third.arrival_date, two_third.dest_timezone), "12:00:00") AS two_third_arrival_time,

TIMEDIFF( two_first.arrival_date, two_first.departure_date) AS flight_time_1,
TIMEDIFF( two_second.arrival_date, two_second.departure_date) AS flight_time_2,
TIMEDIFF( two_third.arrival_date, two_third.departure_date) AS flight_time_3,

case
  when (TIMEDIFF( two_second.arrival_date, two_second.departure_date) IS NOT NULL) AND (TIMEDIFF( two_third.arrival_date, two_third.departure_date) IS NOT NULL)
    then
        ADDTIME(ADDTIME( TIMEDIFF( two_first.arrival_date, two_first.departure_date)
            , TIMEDIFF( two_second.arrival_date, two_second.departure_date)) 
            , TIMEDIFF( two_third.arrival_date, two_third.departure_date))
  when (TIMEDIFF( two_second.arrival_date, two_second.departure_date) IS NOT NULL) AND (TIMEDIFF( two_third.arrival_date, two_third.departure_date) IS NULL)
    then
        ADDTIME( TIMEDIFF( two_first.arrival_date, two_first.departure_date)
            , TIMEDIFF( two_second.arrival_date, two_second.departure_date)) 
  else
        TIMEDIFF( two_first.arrival_date, two_first.departure_date)
end AS flight_time,

case
  when (TIMEDIFF( two_second.departure_date, two_first.arrival_date) IS NOT NULL) AND (TIMEDIFF( two_third.departure_date, two_second.arrival_date) IS NOT NULL)
    then
        ADDTIME(TIMEDIFF( two_second.departure_date, two_first.arrival_date) ,TIMEDIFF( two_third.departure_date, two_second.arrival_date))
  when (TIMEDIFF( two_second.departure_date, two_first.arrival_date) IS NOT NULL) AND (TIMEDIFF( two_third.departure_date, two_second.arrival_date) IS NULL)
    then
        TIMEDIFF( two_second.departure_date, two_first.arrival_date)
  else
        "00:00:00"
end AS transfer_time,

case
  when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
    then
        round((two_first.ticket_price + two_second.ticket_price + two_third.ticket_price) * 0.8)
  when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
    then
        round((two_first.ticket_price + two_second.ticket_price) * 0.9)
  else
        two_first.ticket_price
end AS price,

two_first.id AS two_first_id,
two_first.flight_number AS two_first_flight_number,
two_first.departure AS two_first_departure,
two_first.destination AS two_first_destination,
two_first.departure_date AS two_first_departure_date,
two_first.arrival_date AS two_first_arrival_date,
two_first.depart_timezone AS two_first_depart_timezone,
two_first.dest_timezone AS two_first_dest_timezone,
two_first.ticket_price AS two_first_ticket_price,

two_second.id AS two_second_id,
two_second.flight_number AS two_second_flight_number,
two_second.departure AS two_second_departure,
two_second.destination AS two_second_destination,
two_second.departure_date AS two_second_departure_date,
two_second.arrival_date AS two_second_arrival_date,
two_second.depart_timezone AS two_second_depart_timezone,
two_second.dest_timezone AS two_second_dest_timezone,
two_second.ticket_price AS two_second_ticket_price,

two_third.id AS two_third_id,
two_third.flight_number AS two_third_flight_number,
two_third.departure AS two_third_departure,
two_third.destination AS two_third_destination,
two_third.departure_date AS two_third_departure_date,
two_third.arrival_date AS two_third_arrival_date,
two_third.depart_timezone AS two_third_depart_timezone,
two_third.dest_timezone AS two_third_dest_timezone,
two_third.ticket_price AS two_third_ticket_price

FROM
(
    SELECT flight.id AS id,
    flight.flight_number AS flight_number,
    flight.departure AS departure,
    flight.destination AS destination,
    flight.departure_date AS departure_date,
    flight.arrival_date AS arrival_date,
    flight.ticket_price AS ticket_price,
    A.timezone AS depart_timezone,
    B.timezone AS dest_timezone
    FROM flight
    JOIN airport AS A ON flight.departure = A.name
    JOIN airport AS B ON flight.destination = B.name
) AS two_first
,
(
    (
        SELECT 
        flight.id AS id,
        flight.flight_number AS flight_number,
        flight.departure AS departure,
        flight.destination AS destination,
        flight.departure_date AS departure_date,
        flight.arrival_date AS arrival_date,
        flight.ticket_price AS ticket_price,
        A.timezone AS depart_timezone,
        B.timezone AS dest_timezone
        FROM flight
        JOIN airport AS A ON flight.departure = A.name
        JOIN airport AS B ON flight.destination = B.name
    )
    UNION
    (
        SELECT NULL AS id,
        NULL AS flight_number,
        NULL AS departure,
        NULL AS destination,
        NULL AS departure_date,
        NULL AS arrival_date,
        NULL AS depart_timezone,
        NULL AS dest_timezone,
        0 AS ticket_price
    )
) AS two_second
,
(
    (
        SELECT 
        flight.id AS id,
        flight.flight_number AS flight_number,
        flight.departure AS departure,
        flight.destination AS destination,
        flight.departure_date AS departure_date,
        flight.arrival_date AS arrival_date,
        flight.ticket_price AS ticket_price,
        A.timezone AS depart_timezone,
        B.timezone AS dest_timezone
        FROM flight
        JOIN airport AS A ON flight.departure = A.name
        JOIN airport AS B ON flight.destination = B.name
    )
    UNION
    (
        SELECT NULL AS id,
        NULL AS flight_number,
        NULL AS departure,
        NULL AS destination,
        NULL AS departure_date,
        NULL AS arrival_date,
        NULL AS depart_timezone,
        NULL AS dest_timezone,
        0 AS ticket_price
    )
) AS two_third

WHERE
    (two_first.departure = ?
    AND two_first.destination = ?
    AND two_second.departure IS NULL
    AND two_third.departure IS NULL)
  OR
    (two_first.departure = ?
    AND two_first.destination = two_second.departure
    AND two_second.destination = ?
    AND two_third.departure IS NULL
    AND ADDTIME(two_first.arrival_date,"02:00:00") < two_second.departure_date)
  OR
    (two_first.departure = ?
    AND two_first.destination = two_second.departure
    AND two_second.destination = two_third.departure
    AND two_third.destination = ?
    AND ADDTIME(two_first.arrival_date,"02:00:00") < two_second.departure_date
    AND ADDTIME(two_second.arrival_date,"02:00:00") < two_third.departure_date)
__SQL__;
    $sql .= " ORDER BY " . $order . $order_method;
    //$depart,$dest,$depart,$dest,$depart,$dest
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($depart,$dest,$depart,$dest,$depart,$dest));
    echo <<<__HTML__
    <table class="table table-hover table-condensed">
    <tr>
    <td>Result</td>
    <td>Flight Number</td>
    <td>Departure Airport</td>
    <td>Destination Airport</td>
    <td>Departure Time</td>
    <td>Arrival Time</td>
    <td>Flight Time</td>
    <td>Transfer Time</td>
    <td>Total Time</td>
    <td>Price</td>
    </tr>
__HTML__;
    $count = 0;
    while($data=$sth->fetchObject()){
        if($data->two_second_flight_number !== NULL && $data->two_third_flight_number !== NULL){
            echo '<tr>';
            echo '<td rowspan=3>' . ++$counter . '</td>';
            echo '<td>' . $data->two_first_flight_number . '</td>';
            echo '<td>' . $data->two_first_departure . '</td>';
            echo '<td>' . $data->two_first_destination . '</td>';
            echo '<td>' . $data->two_first_departure_time . '</td>';
            echo '<td>' . $data->two_first_arrival_time . '</td>';
            echo '<td rowspan=3>' . $data->flight_time . '</td>';
            echo '<td rowspan=3>' . $data->transfer_time . '</td>';
            echo '<td rowspan=3>' . $data->total_time . '</td>';
            echo '<td rowspan=3>' . $data->price . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td>' . $data->two_second_flight_number . '</td>';
            echo '<td>' . $data->two_second_departure . '</td>';
            echo '<td>' . $data->two_second_destination . '</td>';
            echo '<td>' . $data->two_second_departure_time . '</td>';
            echo '<td>' . $data->two_second_arrival_time . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td>' . $data->two_third_flight_number . '</td>';
            echo '<td>' . $data->two_third_departure . '</td>';
            echo '<td>' . $data->two_third_destination . '</td>';
            echo '<td>' . $data->two_third_departure_time . '</td>';
            echo '<td>' . $data->two_third_arrival_time . '</td>';
            echo '</tr>';
        }
        else if($data->two_second_flight_number !== NULL){
            echo '<tr>';
            echo '<td rowspan=2>' . ++$counter . '</td>';
            echo '<td>' . $data->two_first_flight_number . '</td>';
            echo '<td>' . $data->two_first_departure . '</td>';
            echo '<td>' . $data->two_first_destination . '</td>';
            echo '<td>' . $data->two_first_departure_time . '</td>';
            echo '<td>' . $data->two_first_arrival_time . '</td>';
            echo '<td rowspan=2>' . $data->flight_time . '</td>';
            echo '<td rowspan=2>' . $data->transfer_time . '</td>';
            echo '<td rowspan=2>' . $data->total_time . '</td>';
            echo '<td rowspan=2>' . $data->price . '</td>';
            echo '</tr>';

            echo '<tr>';
            echo '<td>' . $data->two_second_flight_number . '</td>';
            echo '<td>' . $data->two_second_departure . '</td>';
            echo '<td>' . $data->two_second_destination . '</td>';
            echo '<td>' . $data->two_second_departure_time . '</td>';
            echo '<td>' . $data->two_second_arrival_time . '</td>';
            echo '</tr>';
        }
        else{
            echo '<tr>';
            echo '<td>' . ++$counter . '</td>';
            echo '<td>' . $data->two_first_flight_number . '</td>';
            echo '<td>' . $data->two_first_departure . '</td>';
            echo '<td>' . $data->two_first_destination . '</td>';
            echo '<td>' . $data->two_first_departure_time . '</td>';
            echo '<td>' . $data->two_first_arrival_time . '</td>';
            echo '<td>' . $data->flight_time . '</td>';
            echo '<td>' . $data->transfer_time . '</td>';
            echo '<td>' . $data->total_time . '</td>';
            echo '<td>' . $data->price . '</td>';
            echo '</tr>';
        }
        #echo '<td>' . $data-> . '</td>';
        #echo '<td rowspan=3>' . $data-> . '</td>';
    }
    echo <<<__HTML__
    </table>
__HTML__;
}

?>
