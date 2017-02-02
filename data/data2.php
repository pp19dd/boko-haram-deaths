<?php
// service-mode

define('SERVICE_MODE', true);
include("data.php");

# pre($rewritten_data);
# echo "rows = " . count($rewritten_data);
# die;

$filtered = array();
foreach( $rewritten_data as $row ) {

    // remove unneeded columns
    /*unset( $row["GWNO"] );
    unset( $row["EVENT_ID_CNTY"] );
    unset( $row["EVENT_ID_NO_CNTY"] );
    unset( $row["EVENT_DATE"] );
    unset( $row["TIME_PRECISION"] );
    unset( $row["ACTOR1"] );
    unset( $row["ACTOR2"] );
    unset( $row["INTER1"] );
    unset( $row["ALLY_ACTOR_1"] );
    unset( $row["ALLY_ACTOR_2"] );
    unset( $row["INTER2"] );
    unset( $row["NOTES"] );
    unset( $row["LATLNG"] );
    unset( $row["LATITUDE"] );
    unset( $row["LONGITUDE"] );
    unset( $row["GEO_PRECISION"] );
    unset( $row["SOURCE"] );
    unset( $row["ADMIN2"] );
    unset( $row["ADMIN3"] );
*/
    // service filters rows before you get to it?
    if( isset( $_GET['query']) ) {

        $filter_row = 0;
        foreach( $_GET['query'] as $query_key => $query_value ) {

            if( $row[$query_key] == $query_value ) {
                // pre( $row, true );
                // pre( $_GET['query']);
                $filter_row++;
            }
        }

        if( $filter_row == 0 ) continue;
    }

    $filtered[] = $row;
}

# pre($filtered);
define('SERVICE_MODE_SECOND', true);
$fp = fopen("filtered.csv", "wt");
foreach( $filtered as $row ) {
    fputcsv( $fp, $row );
}
fclose( $fp );
