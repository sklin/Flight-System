<?php
include_once('config.php');

function css_inner_block()
{
    echo <<<__HTML__
    .inner-block {
        width: 820;
    }
__HTML__;
}

function no_transfer($depart,$dest,$order,$method)
{
    accessDB($db);
    $sql = <<<__SQL__
SELECT
    flight.id,
    flight.flight_number,
    flight.departure,
    flight.destination,
    flight.departure_date AS departure_time,
    flight.arrival_date AS arrival_time,
    TIMEDIFF( SUBTIME(flight.arrival_date,.dest.timezone), SUBTIME(flight.departure_date,depart.timezone)) AS flight_time,
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
    <th>#</th>
    <th>Flight Number</th>
    <th>Departure Airport</th>
    <th>Destination Airport</th>
    <th>Departure Time</th>
    <th>Arrival Time</th>
    <th>Flight Time</th>
    <th>Total Flight Time</th>
    <th>Transfer Time</th>
    <th>Price</th>
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
        echo '<td>' . $data->flight_time . '</td>';
        echo '<td>' . 0 . '</td>';
        echo '<td>' . $data->price . '</td>';
        echo '</tr>';
    }
    echo <<<__HTML__
    </table>
__HTML__;
    return $sql;
}

function one_transfer($depart,$dest,$order,$order_method)
{
    accessDB($db);
    $sql =<<<__SQL__
SELECT


TIMEDIFF(
    case
      when one_second.flight_number IS NOT NULL
        then 
            SUBTIME(one_second.arrival_date,one_second.dest_timezone)
        else
            SUBTIME(one_first.arrival_date,one_first.dest_timezone)
    end
    ,
    SUBTIME(one_first.departure_date,one_first.depart_timezone)
)
AS total_time,

one_first.departure_date AS departure_time,

case
  when one_second.flight_number IS NOT NULL
    then
        one_second.arrival_date
  else
        one_first.arrival_date
end AS arrival_time,

SUBTIME( one_first.departure_date, one_first.depart_timezone) AS one_first_departure_time,
SUBTIME( one_first.arrival_date, one_first.dest_timezone) AS one_first_arrival_time,
SUBTIME( one_second.departure_date, one_second.depart_timezone) AS one_second_departure_time,
SUBTIME( one_second.arrival_date, one_second.dest_timezone) AS one_second_arrival_time,

TIMEDIFF( SUBTIME( one_first.arrival_date, one_first.dest_timezone), SUBTIME( one_first.departure_date, one_first.depart_timezone)) AS flight_time_1,
TIMEDIFF( SUBTIME( one_second.arrival_date, one_second.dest_timezone), SUBTIME( one_second.departure_date, one_second.depart_timezone)) AS flight_time_2,

case
  when one_second.flight_number IS NOT NULL
    then
        ADDTIME( 
            TIMEDIFF( SUBTIME( one_first.arrival_date, one_first.dest_timezone),
            SUBTIME( one_first.departure_date, one_first.depart_timezone)
            )
        ,
            TIMEDIFF( SUBTIME( one_second.arrival_date, one_second.dest_timezone),
            SUBTIME( one_second.departure_date, one_second.depart_timezone)
            )
        ) 
    else
        TIMEDIFF( SUBTIME( one_first.arrival_date, one_first.dest_timezone),
                    SUBTIME( one_first.departure_date, one_first.depart_timezone)
                )
end AS flight_time,

case
  when one_second.flight_number IS NOT NULL
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
(one_first.destination = one_second.departure AND one_second.destination = ? AND ADDTIME(one_first.arrival_date,"02:00:00") <= one_second.departure_date)
)
__SQL__;
    $sql .= " ORDER BY " . $order . $order_method;
    //$depart,$dest,$dest
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($depart,$dest,$dest));
    echo <<<__HTML__
        <table class="table table-striped" style="width: 1400px;">
            <tr>
                <td style="width: 50px;">#</td>
                <td style="width: 700px;">
                    <table class="inner-block table-hover">
                        <td style="width: 110px;">Flight Number</td>
                        <td style="width: 130px;">Departure Airport</td>
                        <td style="width: 130px;">Destination Airport</td>
                        <td style="width: 150px;">Departure Time</td>
                        <td style="width: 150px;">Arrival Time</td>
                        <td style="width: 120px;">Flight Time</td>
                    </table>
                </td>
                <td style="width: 120px;">Total Flight Time</td>
                <td style="width: 120px;">Transfer Time</td>
                <td style="width: 120px;">Total Time</td>
                <td style="width: 120px;">Price</td>
            </tr>
__HTML__;

    $counter = 0;
    while($data=$sth->fetchObject()){
        if($data->one_second_flight_number !== NULL){
            echo '<tr>';
            echo '<td style="width: 50px;">' . ++$counter . '</td>';
            echo <<<__HTML__
            <td style="width: 700px;">
            <table class="inner-block table-hover">
                <tr><td style="width: 110px;">{$data->one_first_flight_number}</td>
                <td style="width: 130px;">{$data->one_first_departure}</td>
                <td style="width: 130px;">{$data->one_first_destination}</td>
                <td style="width: 150px;">{$data->one_first_departure_date}</td>
                <td style="width: 150px;">{$data->one_first_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_1}</td></tr>
                <tr><td style="width: 110px;">{$data->one_second_flight_number}</td>
                <td style="width: 130px;">{$data->one_second_departure}</td>
                <td style="width: 130px;">{$data->one_second_destination}</td>
                <td style="width: 150px;">{$data->one_second_departure_date}</td>
                <td style="width: 150px;">{$data->one_second_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_2}</td></tr>
            </table>
            </td>
__HTML__;
            echo '<td style="width: 120px;">' . $data->flight_time . '</td>';
            echo '<td style="width: 120px;">' . $data->transfer_time . '</td>';
            echo '<td style="width: 120px;">' . $data->total_time . '</td>';
            echo '<td style="width: 120px;">' . $data->price . '</td>';
            echo '</tr>';

        }
        else{
            echo '<tr>';
            echo '<td style="width: 50px;">' . ++$counter . '</td>';
            echo '<td style="width: 700px;"><table class="inner-block table-hover">';
                echo '<td style="width: 110px;">' . $data->one_first_flight_number . '</td>';
                echo '<td style="width: 130px;">' . $data->one_first_departure . '</td>';
                echo '<td style="width: 130px;">' . $data->one_first_destination . '</td>';
                echo '<td style="width: 150px;">' . $data->one_first_departure_date . '</td>';
                echo '<td style="width: 150px;">' . $data->one_first_arrival_date . '</td>';
                echo '<td style="width: 150px;">' . $data->flight_time_1 . '</td>';
            echo '</table></td>';
            echo '<td style="width: 120px;">' . $data->flight_time . '</td>';
            echo '<td style="width: 120px;">' . $data->transfer_time . '</td>';
            echo '<td style="width: 120px;">' . $data->total_time . '</td>';
            echo '<td style="width: 120px;">' . $data->price . '</td>';
            echo '</tr>';
        }

    }
    echo <<<__HTML__
    </table>
__HTML__;
    return $sql;
}

function two_transfer($depart,$dest,$order,$order_method)
{
    accessDB($db);
    $sql = <<<__SQL__
SELECT

TIMEDIFF(
    case
      when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
        then 
            SUBTIME(two_third.arrival_date,two_third.dest_timezone)
      when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
        then
            SUBTIME(two_second.arrival_date,two_second.dest_timezone)
      else
            SUBTIME(two_first.arrival_date,two_first.dest_timezone)
    end
    ,
    SUBTIME(two_first.departure_date,two_first.depart_timezone)
) AS total_time,

two_first.departure_date AS departure_time,

case
  when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
    then
        two_third.arrival_date
  when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
    then
        two_second.arrival_date
  else
        two_first.arrival_date
end AS arrival_time,

SUBTIME( two_first.departure_date, two_first.depart_timezone) AS two_first_departure_time,
SUBTIME( two_first.arrival_date, two_first.dest_timezone) AS two_first_arrival_time,
SUBTIME( two_second.departure_date, two_second.depart_timezone) AS two_second_departure_time,
SUBTIME( two_second.arrival_date, two_second.dest_timezone) AS two_second_arrival_time,
SUBTIME( two_third.departure_date, two_third.depart_timezone) AS two_third_departure_time,
SUBTIME( two_third.arrival_date, two_third.dest_timezone) AS two_third_arrival_time,

TIMEDIFF( SUBTIME( two_first.arrival_date, two_first.dest_timezone), SUBTIME( two_first.departure_date, two_first.depart_timezone)) AS flight_time_1,
TIMEDIFF( SUBTIME( two_second.arrival_date, two_second.dest_timezone), SUBTIME( two_second.departure_date, two_second.depart_timezone)) AS flight_time_2,
TIMEDIFF( SUBTIME( two_third.arrival_date, two_third.dest_timezone), SUBTIME( two_third.departure_date, two_third.depart_timezone)) AS flight_time_3,

case
  when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
    then
        ADDTIME(
            ADDTIME(
                TIMEDIFF(
                    SUBTIME( two_first.arrival_date, two_first.dest_timezone)
                    ,
                    SUBTIME( two_first.departure_date, two_first.depart_timezone)
                )
                ,
                TIMEDIFF(
                    SUBTIME( two_second.arrival_date, two_second.dest_timezone)
                    ,
                    SUBTIME( two_second.departure_date, two_second.depart_timezone)
                )
            )
            ,
            TIMEDIFF(
                SUBTIME( two_third.arrival_date, two_third.dest_timezone)
                ,
                SUBTIME( two_third.departure_date, two_third.depart_timezone)
            )
        )
  when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
    then
        ADDTIME(
            TIMEDIFF(
                SUBTIME( two_first.arrival_date, two_first.dest_timezone)
                ,
                SUBTIME( two_first.departure_date, two_first.depart_timezone)
            )
        , TIMEDIFF(
            SUBTIME( two_second.arrival_date, two_second.dest_timezone)
            ,
            SUBTIME( two_second.departure_date, two_second.depart_timezone)
            )
        )
  else
        TIMEDIFF(
            SUBTIME( two_first.arrival_date, two_first.dest_timezone),
            SUBTIME( two_first.departure_date, two_first.depart_timezone)
        )
end AS flight_time,

case
  when (two_third.flight_number IS NOT NULL) AND (two_second.flight_number IS NOT NULL)
    then
        ADDTIME(TIMEDIFF( two_second.departure_date, two_first.arrival_date) ,TIMEDIFF( two_third.departure_date, two_second.arrival_date))
  when (two_third.flight_number IS NULL) AND (two_second.flight_number IS NOT NULL)
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
    AND ADDTIME(two_first.arrival_date,"02:00:00") <= two_second.departure_date)
  OR
    (two_first.departure = ?
    AND two_first.destination = two_second.departure
    AND two_second.destination = two_third.departure
    AND two_second.destination <> two_first.departure
    AND two_third.destination = ?
    AND ADDTIME(two_first.arrival_date,"02:00:00") <= two_second.departure_date
    AND ADDTIME(two_second.arrival_date,"02:00:00") <= two_third.departure_date)
__SQL__;
    $sql .= " ORDER BY " . $order . $order_method;
    //$depart,$dest,$depart,$dest,$depart,$dest
    $sth = $db->prepare($sql);
    $result = $sth->execute(array($depart,$dest,$depart,$dest,$depart,$dest));
    echo <<<__HTML__
    <table class="table table-striped" style="width: 1400px;">
    <tr>
    <td style="width: 50px;">#</td>
    <td style="width: 700px;">
        <table class="inner-block table-hover">
            <td style="width: 110px;">Flight Number</td>
            <td style="width: 130px;">Departure Airport</td>
            <td style="width: 130px;">Destination Airport</td>
            <td style="width: 150px;">Departure Time</td>
            <td style="width: 150px;">Arrival Time</td>
            <td style="width: 150px;">Flight Time</td>
        </table>
    </td>
    <td style="width: 120px;">Total Flight Time</td>
    <td style="width: 120px;">Transfer Time</td>
    <td style="width: 120px;">Total Time</td>
    <td style="width: 120px;">Price</td>
    </tr>
__HTML__;
    $count = 0;
    #echo "QQQQQ".var_dump($result);
    #echo var_dump($sth->errorInfo());
    while($data=$sth->fetchObject()){
        if($data->two_second_flight_number !== NULL && $data->two_third_flight_number !== NULL){
            echo '<tr>';
            echo '<td style="width: 50px;">' . ++$counter . '</td>';
            echo <<<__HTML__
            <td style="width: 700px;">
            <table class="inner-block table-hover">
                <tr><td style="width: 110px;">{$data->two_first_flight_number}</td>
                <td style="width: 130px;">{$data->two_first_departure}</td>
                <td style="width: 130px;">{$data->two_first_destination}</td>
                <td style="width: 150px;">{$data->two_first_departure_date}</td>
                <td style="width: 150px;">{$data->two_first_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_1}</td></tr>
                <td style="width: 110px;">{$data->two_second_flight_number}</td>
                <td style="width: 130px;">{$data->two_second_departure}</td>
                <td style="width: 130px;">{$data->two_second_destination}</td>
                <td style="width: 150px;">{$data->two_second_departure_date}</td>
                <td style="width: 150px;">{$data->two_second_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_2}</td></tr>
                <tr><td style="width: 110px;">{$data->two_third_flight_number}</td>
                <td style="width: 130px;">{$data->two_third_departure}</td>
                <td style="width: 130px;">{$data->two_third_destination}</td>
                <td style="width: 150px;">{$data->two_third_departure_date}</td>
                <td style="width: 150px;">{$data->two_third_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_3}</td></tr>
            </table>
            </td>
__HTML__;
            echo '<td style="width: 120px;">' . $data->flight_time . '</td>';
            echo '<td style="width: 120px;">' . $data->transfer_time . '</td>';
            echo '<td style="width: 120px;">' . $data->total_time . '</td>';
            echo '<td style="width: 120px;">' . $data->price . '</td>';
            echo '</tr>';
        }
        else if($data->two_second_flight_number !== NULL){
            echo '<tr>';
            echo '<td style="width: 50px;">' . ++$counter . '</td>';
            echo <<<__HTML__
            <td style="width: 700px;">
            <table class="inner-block table-hover">
                <tr><td style="width: 110px;">{$data->two_first_flight_number}</td>
                <td style="width: 130px;">{$data->two_first_departure}</td>
                <td style="width: 130px;">{$data->two_first_destination}</td>
                <td style="width: 150px;">{$data->two_first_departure_date}</td>
                <td style="width: 150px;">{$data->two_first_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_1}</td></tr>
                <td style="width: 110px;">{$data->two_second_flight_number}</td>
                <td style="width: 130px;">{$data->two_second_departure}</td>
                <td style="width: 130px;">{$data->two_second_destination}</td>
                <td style="width: 150px;">{$data->two_second_departure_date}</td>
                <td style="width: 150px;">{$data->two_second_arrival_date}</td>
                <td style="width: 150px;">{$data->flight_time_2}</td></tr>
            </table>
            </td>
__HTML__;
            echo '<td style="width: 120px;">' . $data->flight_time . '</td>';
            echo '<td style="width: 120px;">' . $data->transfer_time . '</td>';
            echo '<td style="width: 120px;">' . $data->total_time . '</td>';
            echo '<td style="width: 120px;">' . $data->price . '</td>';
            echo '</tr>';

        }
        else{
            echo '<tr>';
            echo '<td style="width: 50px;">' . ++$counter . '</td>';
            echo '<td style="width: 700px;"><table>';
                echo '<td style="width: 100px;">' . $data->two_first_flight_number . '</td>';
                echo '<td style="width: 120px;">' . $data->two_first_departure . '</td>';
                echo '<td style="width: 120px;">' . $data->two_first_destination . '</td>';
                echo '<td style="width: 140px;">' . $data->two_first_departure_date . '</td>';
                echo '<td style="width: 140px;">' . $data->two_first_arrival_date . '</td>';
                echo '<td style="width: 140px;">' . $data->flight_time_1 . '</td>';
                #echo '<td style="width: 110px;">' . $data->two_first_flight_number . '</td>';
                #echo '<td style="width: 130px;">' . $data->two_first_departure . '</td>';
                #echo '<td style="width: 130px;">' . $data->two_first_destination . '</td>';
                #echo '<td style="width: 150px;">' . $data->two_first_departure_time . '</td>';
                #echo '<td style="width: 150px;">' . $data->two_first_arrival_time . '</td>';
            echo '</table></td>';
            echo '<td style="width: 120px;">' . $data->flight_time . '</td>';
            echo '<td style="width: 120px;">' . $data->transfer_time . '</td>';
            echo '<td style="width: 120px;">' . $data->total_time . '</td>';
            echo '<td style="width: 120px;" style="width: 120px;">' . $data->price . '</td>';
            echo '</tr>';
        }
        #echo '<td>' . $data-> . '</td>';
        #echo '<td rowspan=3>' . $data-> . '</td>';
    }
    echo <<<__HTML__
    </table>
__HTML__;
    return $sql;
}

?>
