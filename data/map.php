<?php

// cheap workaround for XML namespaces
$svg = file_get_contents("nigeria.svg");
$pos_a = stripos($svg, "</metadata>");
$svg = "<svg>" . substr($svg, $pos_a + 11);

// lets get state G's
$doc = new DOMDocument();
@$doc->loadXML($svg);
$xpath = new DOMXPath($doc);
$g = $xpath->query("//g");

$ret = array();

// reconstitute as json
foreach( $g as $group ) {
    $paths = $xpath->query("path", $group);
    if( $paths->length === 0 ) continue;

    $id = $group->getAttribute("id");
    $d = $paths->item(0)->getAttribute("d");

    $ret[$id] = $d;
}
#echo $g->length;
file_put_contents("map.json", json_encode( $ret, JSON_PRETTY_PRINT ));
