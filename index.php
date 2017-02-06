<!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
<link href="project.css?ts=<?php echo time() ?>" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="pym.v1.min.js"></script>
<script src="js/raphael.min.js"></script>
<script src="js/rainbowvis.js"></script>
<script src="plot.js?ts=<?php echo time() ?>"></script>
<script src="plot-map.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content class="noselect">
<!--
        <presets>
            <preset onclick="preset(1, this)">Most violence throughout the years occurred in Borno state.</preset>
            <preset onclick="preset(2, this)">Boko Haram involvement has ticked up.</preset>
            <preset onclick="preset(3, this)">2014 Friday Activity</preset>
            <preset onclick="preset(4, this)">Reset Filters</preset>
        </presets>
        <h3>Fatalities in Nigeria</h3>

        <div style="float:right">
            <h3>Boko Haram as Actor</h3>
            <div class="chart" id="boko"></div>
        </div>
-->
        <div class="chart" id="timeline"></div>
<!--
<div class="chart" id="e_dow" style="float:right"></div>
-->
        <div class="chart" id="map"></div>


    </content>
<!--
    <clr></clr>
    <credits>
        <p><strong>Data Source</strong>: Armed Conflict Location &amp; Event Data Project.</p>
        <p>The ACLED data is derived from media and secondary reports.  It may be subject to error and likely undereports the number of fatalities.</p>
        <p>These figures do not include deaths of terrorists or Nigerian police or military.</p>
    </credits>
-->
<script>

var json_data = <?php readfile("data-clean-2016.json"); ?>;
var json_map = <?php readfile("map.json"); ?>;

// ===========================================================================
// re-inflate data in memory
// ===========================================================================
for( var i = 0; i < json_data.rows.length; i ++ ) {
    var place_name = json_data.keys.shorten.ADMIN1[json_data.rows[i].l];
    place_name = place_name.toLowerCase();
    place_name = place_name.split(" ").join("_");
    json_data.rows[i].place_name = place_name;


}

var e_map = new smartmap("map", 800, 358);
e_map.paper.setViewBox(0, 155, 525, 390);
for( var state in json_map )(function(key, path) {
    e_map.addPlace(key, path);
})(state, json_map[state]);

// ===========================================================================
// currently global
// ===========================================================================
var filters = { };

// ===========================================================================
// incidents over time
// ===========================================================================
var e_timeline = new smartbox("timeline", 800, 150);
// var e_boko = new smartbox("boko", 380, 80);
// var e_dow = new smartbox("e_dow", 220, 270);

e_timeline.setData(json_data.rows, "y", "f");
e_map.setData(json_data.rows, "place_name", "f");
// e_boko.setData(json_data.rows, "b", "f");
// e_dow.setData(json_data.rows, "d", "f");

// ===========================================================================
// filtration system is temporary
// ===========================================================================
function join_filters() {
    reset_menu();

    e_timeline.applyFilters(filters);
    e_map.applyFilters(filters);
//    e_boko.applyFilters(filters);
//    e_dow.applyFilters(filters);

    e_timeline.doDraw();
    e_map.doDraw();
//    e_boko.doDraw();
//    e_dow.doDraw();
}

e_timeline.setFilterEvent( join_filters );
e_map.setFilterEvent( join_filters );
// e_boko.setFilterEvent( join_filters );
// e_dow.setFilterEvent( join_filters );


// ===========================================================================
// last minute fix, namely map draw
// ===========================================================================
for( var state in json_map )(function(key, path) {
    if( typeof e_map.data[state] == "undefined" ) {
        e_map.data[state] = 0;
    }
})(state, json_map[state]);
e_map.e.water_body.__disable = true;
e_map.e.water_body.attr({ cursor: 'default'});



// ===========================================================================
// draw main components
// ===========================================================================
e_timeline
    .setMargin("all", 10)
    .setMargin("spacing", 50)
    .doDraw();

e_map
    .doDraw();

// e_dow
//     .setVertical()
//     .setAnchorTop()
//     .doDraw();
//
// e_boko
//     .setVertical()
//     .setAnchorTop()
//     .doDraw();

// ===========================================================================
// webpage menus and interaction
// ===========================================================================
function reset_menu() {
    var presets = document.querySelectorAll("preset");
    for( var i = 0; i < presets.length; i++ ) {
        presets[i].className = "";
    }
}

function preset(p, that) {
    switch( p ) {
        case 1: filters = { place_name: { key: "place_name", value: "borno" } }; break;
        case 2: filters = { b: { key: "b", value: "Y" } }; break;
        case 3: filters = { y: { key: "y", value: "2014" } }; break;
        case 4: filters = {}; break;
    }

    join_filters();
    that.className = "active";
}

e_timeline.paper.setViewBox(0,0,800,150);

var pc = new pym.Child();

function resize_event_handler() {
    var w = $(window).width() - 30;
    var h = $(window).height();

    var h1 = (w * 150) / 830;
    var h2 = (w * 740) / 830;

    e_timeline.paper.setSize(w, h1);
    e_map.paper.setSize(w, h2-h1);

    pc.sendHeight(h);
}

$(window).resize(function() {
    resize_event_handler();
});

resize_event_handler();

</script>
</body>
</html>
