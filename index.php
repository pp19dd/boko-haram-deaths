<!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet" />
<link href="project.css?ts=<?php echo time() ?>" rel="stylesheet" />
<script src="js/raphael.min.js"></script>
<script src="js/rainbowvis.js"></script>
<script src="plot.js?ts=<?php echo time() ?>"></script>
<script src="plot-map.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content class="noselect">
        <presets>
            <preset onclick="preset(1, this)">Most violence throughout the years occurred in Borno state.</preset>
            <preset onclick="preset(2, this)">Boko Haram involvement has ticked up.</preset>
            <preset onclick="preset(3, this)">Reset Filters</preset>
        </presets>

        <h3>Fatalities in Nigeria</h3>

        <div style="float:right">
            <h3>Boko Haram as Actor</h3>
            <div class="chart" id="boko"></div>
        </div>
        <div class="chart" id="timeline"></div>
        <div class="chart" id="map" style="float:left"></div>

    </content>
    <div id="chart"></div>
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

var e_map = new smartmap("map", 800, 600);
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
var e_timeline = new smartbox("timeline", 400, 150);
var e_boko = new smartbox("boko", 100, 80);

e_timeline.setData(json_data.rows, "y", "f");
e_map.setData(json_data.rows, "place_name", "f");
e_boko.setData(json_data.rows, "b", "f");

// ===========================================================================
// filtration system is temporary
// ===========================================================================
function join_filters() {
    reset_menu();

    e_timeline.applyFilters(filters);
    e_map.applyFilters(filters);
    e_boko.applyFilters(filters);

    e_timeline.doDraw();
    e_map.doDraw();
    e_boko.doDraw();
}

e_timeline.setFilterEvent( join_filters );
e_map.setFilterEvent( join_filters );
e_boko.setFilterEvent( join_filters );


// ===========================================================================
// last minute fix, namely map draw
// ===========================================================================
for( var state in json_map )(function(key, path) {
    if( typeof e_map.data[state] == "undefined" ) {
        e_map.data[state] = 0;
        //console.info( state );
    }
})(state, json_map[state]);

// ===========================================================================
// draw main components
// ===========================================================================
e_timeline
    .setMargin("all", 10)
    .doDraw();

e_map
    .doDraw();

e_boko
    .setVertical()
    .setAnchorTop()
    .doDraw();

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
        case 3: filters = {}; break;
    }

    join_filters();
    that.className = "active";
}

</script>
</body>
</html>
