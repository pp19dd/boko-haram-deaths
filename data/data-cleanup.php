<?php
define( "CSV_FILE_INPUT", "ACLED_Nigeria_2016.csv" );
define( "JSON_FILE_OUTPUT", "data-clean-2016.json" );

# define( "LIMIT", 10 );

// these columns are not needed for the interactive
// original file size: 6.9 mb
//      after removal: 1.4 mb
$remove = array(
    "LATLNG", "NOTES", "ACTOR1", "ACTOR2", "ALLY_ACTOR_1", "ALLY_ACTOR_2",
    "ADMIN2", "LOCATION", "SOURCE", "DAY_OF_WEEK_PER_YEAR", "EVENT_DATE",
    "GWNO", "EVENT_ID_CNTY", "EVENT_ID_NO_CNTY", "TIME_PRECISION", "INTER1",
    "INTER2", "INTERACTION", "ADMIN3", "LATITUDE", "LONGITUDE",
    "GEO_PRECISION", "COUNTRY"
);

// original file size: 1.4 mb
//      after removal: 0.8 mb
$rename = array(
    "YEAR" => "y",
    "EVENT_TYPE" => "e",
    "ADMIN1" => "l",
    "DAY_OF_WEEK" => "d",
    "BOKO_HARAM_INVOLVED" => "b",
    "FATALITIES" => "f",
    "FATALITIES_BIN" => "g"
);

// original file size: 0.8 mb
//      after removal: 0.5 mb
$shorten = array(
    "EVENT_TYPE" => array(
        "Violence against civilians" => "1",
        "Riots/Protests" => "2",
        "Battle-No change of territory" => "3",
        "Remote violence" => "4",
        "Strategic development" => "5",
        "Battle-Government regains territory" => "6",
        "Battle-Non-state actor overtakes territory" => "7",
        "Non-violent transfer of territory" => "8",
        "Headquarters or base established" => "9"
    ),
    "DAY_OF_WEEK" => array(
        "Monday" => "M",
        "Tuesday" => "T",
        "Wednesday" => "W",
        "Thursday" => "Th",
        "Friday" => "F",
        "Saturday" => "S",
        "Sunday" => "Su"
    ),
    "BOKO_HARAM_INVOLVED" =>
        array(
            "yes" => "Y",
            "no" => "N"
        ),
    "ADMIN1" => array(
        "Abia" => 0, "Adamawa" => 1, "Akwa Ibom" => 2, "Anambra" => 3,
        "Bauchi" => 4, "Bayelsa" => 5, "Benue" => 6, "Borno" => 7,
        "Cross River" => 8, "Delta" => 9, "Ebonyi" => 10, "Edo" => 11,
        "Ekiti" => 12, "Enugu" => 13, "Federal Capital Territory" => 14,
        "Gombe" => 15, "Imo" => 16, "Jigawa" => 17, "Kaduna" => 18,
        "Kano" => 19, "Katsina" => 20, "Kebbi" => 21, "Kogi" => 22,
        "Kwara" => 23, "Lagos" => 24, "Nasarawa" => 25, "Niger" => 26,
        "Ogun" => 27, "Ondo" => 28, "Osun" => 29, "Oyo" => 30,
        "Plateau" => 31, "Rivers" => 32, "Sokoto" => 33, "Taraba" => 34,
        "Yobe" => 35, "Zamfara" => 36,
    )
);

// quickfix
foreach( $shorten["ADMIN1"] as $k => $v ) {
    $shorten["ADMIN1"][$k] = $v + 1;
}

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

$fp = fopen(CSV_FILE_INPUT, "rt");
$h = fgetcsv($fp);
$data = array();

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

    // fix for json output
    foreach( $r as $k => $v ) {
        $r[$k] = utf8_encode($v);
    }

    $row = array_combine(array_values($h), $r);

    foreach( $row as $k => $v) {

        // boko-haram as a participant?
        if( $k === "ACTOR1" || $k === "ACTOR2" ) {
            if( stripos($v, "boko") !== false ) {
                $row["BOKO_HARAM_INVOLVED"] = "yes";
            } else {
                $row["BOKO_HARAM_INVOLVED"] = "no";
            }
        }

        if( $k == "GWNO" ) continue;
        if( $k == "EVENT_ID_NO_CNTY" ) continue;
        if( $k == "EVENT_ID_CNTY" ) continue;
        if( $k == "EVENT_DATE" ) {
            $dow = format_date($v, 2);
            $row["DAY_OF_WEEK"] = $dow;

            $dow = format_date($v, 3);
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
            $row["LATLNG"] = $v;
            continue;
        }
        if( $k == "LONGITUDE" ) continue;
        if( $k == "GEO_PRECISION" ) continue;

        // may want to bin fatalities. max value = 6766
        $row["FATALITIES_BIN"] = 0;
        if( $k == "FATALITIES" ) {
            if( intval($v) === 0 ) continue;

            $orig = $v;
            $num = intval($v / 5) * 5;
            $v = sprintf("%4s - %4s", $num+1, $num + 5);

            $row["FATALITIES_BIN"] = $v;
            // note zeroes are not computed in fatalities bin
            #echo "" . $orig . "\t" . $v . "\n";
        }
    }

    foreach( $remove as $col ) {
        unset( $row[$col] );
    }

    foreach( $shorten as $field_name => $answers ) {
        if( !isset( $row[$field_name]) ) continue;
        foreach( $answers as $value_from => $value_to ) {
            if( $row[$field_name] === $value_from ) {
                $row[$field_name] = $value_to;
            }
        }
    }

    foreach( $rename as $old_key => $new_key ) {
        if( !isset( $row[$old_key]) ) continue;

        $row[$new_key] = $row[$old_key];
        unset( $row[$old_key] );
    }

    $data[] = $row;

    $count++;
    if( defined("LIMIT") && $count > LIMIT ) break;
}
fclose( $fp );

foreach( $shorten as $field_name => $answers ) {
    $shorten[$field_name] = array_flip($answers);
}

$output = array(
    "keys" => array(
        "field_names" => array(
            array_flip($rename)
        ),
        "shorten" => $shorten
    ),
    "rows" => $data
);

file_put_contents(JSON_FILE_OUTPUT, json_encode($output) );
echo number_format(filesize(JSON_FILE_OUTPUT)) . " bytes";


#pre( json_encode($output, JSON_PRETTY_PRINT) );
#echo json_encode($output);
