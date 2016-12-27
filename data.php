<?php
#define( "LIMIT", 5 );
srand(filemtime(__FILE__));
function pre($a, $live = false) {
    printf(
        "<PRE style='padding:1em; background-color:rgb(%s,%s,%s)'>",
        rand(150, 255),
        rand(150, 255),
        rand(150, 255)
    );
    print_r( $a );
    echo "</PRE>";
    if( $live === false ) die;
}

$count = 0;
$fp = fopen("ACLED_Nigeria.csv", "rt");
$h = fgetcsv($fp);
$data = array();
$unique = array();
foreach( $h as $v ) {
    $unique[$v] = array();
}

// add some custom fields
$unique["LATLNG"] = array();
$unique["DAY_OF_WEEK"] = array();
$unique["DAY_OF_WEEK_PER_YEAR"] = array();

function format_date($v, $mode = 1 ) {
    $x = explode("/", $v);
    $tss = sprintf("%s-%s-%s", $x[2], $x[1], $x[0]);
    $ts = strtotime($tss);

    switch( $mode ) {
        case 1: return( date("M Y", $ts) ); break;
        case 2: return( date("l", $ts) ); break;
        case 3: return( date("Y-l", $ts) ); break;
    }
}

while( !feof($fp) ) {
    $r = fgetcsv($fp);
    if( $r === false ) continue;

    $row = array_combine(array_values($h), $r);
    $data[] = $row;

    foreach( $row as $k => $v) {

        if( $k == "GWNO" ) continue;
        if( $k == "EVENT_ID_NO_CNTY" ) continue;
        if( $k == "EVENT_ID_CNTY" ) continue;
        if( $k == "EVENT_DATE" ) {
            $dow = format_date($v, 2);
            if( !isset($unique["DAY_OF_WEEK"][$dow]) ) $unique["DAY_OF_WEEK"][$dow] = 0;
            $unique["DAY_OF_WEEK"][$dow]++;

            $dow = format_date($v, 3);
            if( !isset($unique["DAY_OF_WEEK_PER_YEAR"][$dow]) ) $unique["DAY_OF_WEEK_PER_YEAR"][$dow] = 0;
            $unique["DAY_OF_WEEK_PER_YEAR"][$dow]++;

            $v = format_date($v, 1);
        }
        if( $k == "TIME_PRECISION" ) continue;
        if( $k == "INTER1" ) continue;
        if( $k == "INTER2" ) continue;
        if( $k == "INTERACTION" ) continue;
        if( $k == "COUNTRY" ) continue;
        if( $k == "ADMIN3" ) continue;
        if( $k == "LATITUDE" ) {
            $v = $row["LATITUDE"] . ", " . $row["LONGITUDE"];
            if( !isset($unique["LATLNG"][$v]) ) $unique["LATLNG"][$v] = 0;
            $unique["LATLNG"][$v]++;
            continue;
        }
        if( $k == "LONGITUDE" ) continue;
        if( $k == "GEO_PRECISION" ) continue;

        // may want to bin fatalities. max value = 6766
        if( $k == "FATALITIES" ) {
            $num = intval($v / 5) * 5;
            $v = sprintf("%4s - %4s", $num, $num + 5);
        }

        if( !isset($unique[$k][$v]) ) $unique[$k][$v] = 0;
        $unique[$k][$v]++;
    }
    $count++;
    if( defined("LIMIT") && $count > LIMIT ) break;
}
fclose( $fp );

// reverse sort
$final = array();
$sort_these = array(
    "EVENT_TYPE", "ACTOR1", "ALLY_ACTOR_1", "ACTOR2", "ALLY_ACTOR_2",
    "ADMIN1", "ADMIN2", "LOCATION", "LATITUDE", "SOURCE",
    "DAY_OF_WEEK"//, "DAY_OF_WEEK_PER_YEAR" //, "FATALITIES"
);
foreach( $unique as $k => $v ) {
    if( empty($v) ) continue;
    $final[$k] = $v;

    if( in_array($k, $sort_these) ) arsort($final[$k]);
}

// for legibility
ksort($final["FATALITIES"]);

$unique = $final;

#pre(count($unique["ACTOR2"]));
#pre($h, true);
#pre($count, true );

#pre($unique);
#pre($data);
