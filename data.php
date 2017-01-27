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
$unique["BOKO_HARAM"] = array("yes" => 0, "no" => 0);

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

$rewritten_data = array();

while( !feof($fp) ) {
    $r = fgetcsv($fp);
    if( $r === false ) continue;

    // fix for json output
    foreach( $r as $k => $v ) {
        $r[$k] = utf8_encode($v);
    }

    $row = array_combine(array_values($h), $r);
    $data[] = $row;

    foreach( $row as $k => $v) {

        // boko-haram as a participant?
        if( $k === "ACTOR1" || $k === "ACTOR2" ) {
            if( stripos($v, "boko") !== false ) {
                $unique["BOKO_HARAM"]["yes"]++;
                $row["BOKO_HARAM"] = "yes";
            } else {
                $unique["BOKO_HARAM"]["no"]++;
                $row["BOKO_HARAM"] = "no";
            }
        }

        if( $k == "GWNO" ) continue;
        if( $k == "EVENT_ID_NO_CNTY" ) continue;
        if( $k == "EVENT_ID_CNTY" ) continue;
        if( $k == "EVENT_DATE" ) {
            $dow = format_date($v, 2);
            if( !isset($unique["DAY_OF_WEEK"][$dow]) ) $unique["DAY_OF_WEEK"][$dow] = 0;
            $unique["DAY_OF_WEEK"][$dow]++;
            $row["DAY_OF_WEEK"] = $dow;

            $dow = format_date($v, 3);
            if( !isset($unique["DAY_OF_WEEK_PER_YEAR"][$dow]) ) $unique["DAY_OF_WEEK_PER_YEAR"][$dow] = 0;
            $unique["DAY_OF_WEEK_PER_YEAR"][$dow]++;
            $row["DAY_OF_WEEK_PER_YEAR"] = $dow;

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
            $row["LATLNG"] = $v;
            $rewritten_data[] = $row;
            continue;
        }
        if( $k == "LONGITUDE" ) continue;
        if( $k == "GEO_PRECISION" ) continue;

        // may want to bin fatalities. max value = 6766
        if( $k == "FATALITIES" ) {
            if( intval($v) === 0 ) continue;

            $orig = $v;
            $num = intval($v / 5) * 5;
            $v = sprintf("%4s - %4s", $num+1, $num + 5);

            $row["FATALITIES_BIN"] = $v;
            // note zeroes are not computed in fatalities bin
            #echo "" . $orig . "\t" . $v . "\n";

        }

        if( !isset($unique[$k][$v]) ) $unique[$k][$v] = 0;
        $unique[$k][$v]++;
    }

    $rewritten_data[] = $row;
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
#pre( $unique["FATALITIES"]);

#pre(count($unique["ACTOR2"]));
#pre($h, true);
#pre($count, true );

#pre($unique);
#pre($data);

// for service
unset( $unique["LATLNG"] );
unset( $unique["NOTES"] );
unset( $unique["ACTOR1"] );
unset( $unique["ACTOR2"] );
unset( $unique["ALLY_ACTOR_1"] );
unset( $unique["ALLY_ACTOR_2"] );
unset( $unique["ADMIN2"] );
unset( $unique["LOCATION"] );
unset( $unique["SOURCE"] );
unset( $unique["DAY_OF_WEEK_PER_YEAR"] );
unset( $unique["EVENT_DATE"] );

ksort( $unique["ADMIN1"] );
$temp = array_keys($unique["ADMIN1"]);
$temp = array_flip($temp);
pre($temp);

// output as external file
// file_put_contents("data.json", json_encode($unique) );

// echo "<PRE>";
// header("Content-Type: application/json");
//if( !defined('SERVICE_MODE') ) {
$fp = fopen("for_sql.csv", "wt");
fputcsv($fp, array_keys($rewritten_data[0]) );
foreach( $rewritten_data as $row ) {
    fputcsv( $fp, $row );
}
fclose( $fp );

die;
echo json_encode($unique, JSON_PRETTY_PRINT);
//}
// if( defined("SERVICE_MODE_SECOND")) {
//     echo json_encode($unique, JSON_PRETTY_PRINT);
// }
// echo number_format(filesize("data.json")) . " bytes";
