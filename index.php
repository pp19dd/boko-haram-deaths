<!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
<style>
body, html {
    height: 100%;
    width: 100%;
    margin:0;
    padding:0;
    background-color: #222;
    color: #DDDDDD;
}
h3 {
    xbackground-color: #AAAAAA;
    xcolor: #111;
    color: #3D9970;
    padding:0.25em;
    font-family: 'Roboto', sans-serif;
    display: inline;

}

/* thanks, s/o # 4407335 */
.noselect {
  -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; /* Non-prefixed version, currently
                                  supported by Chrome and Opera */
}

content {
    padding: 1em; display: block; width: 1150px;
}
.chart { margin-bottom: 1em }
#timeline { width: 800px; height:150px; }
#fatalities { width: 800px; height:150px; }
#map  { width: 800px; height:600px; }
#e_type { width: 300px; height:400px; }
#dow { width: 200px; height:300px; }

#bh { width: 300px; height:300px;  }

svg tspan, svg text { pointer-events: none }

presets { display: flex; margin-bottom:2em;  }
preset {
    padding: 1em;
    cursor: pointer;
    font-family: 'Roboto', sans-serif;
    font-size: 1.15em;
    flex-basis: 0;
    flex-grow: 2;
}
preset:hover { text-decoration: underline; color: white }
.active  { background-color: #AAAAAA }
</style>
<script src="js/raphael.min.js"></script>
<script src="js/rainbowvis.js"></script>
<script src="plot.js?ts=<?php echo time() ?>"></script>
<script src="plot-map.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content class="noselect">
        <presets>
            <preset onclick="preset(1, this)">Most violence throughout the years occurred in Borno state.</preset>
            <preset onclick="preset(2, this)">There has been a noticable downtick on Friday killings in 2015.</preset>
            <preset onclick="preset(3, this)">Boko Haram involvement has ticked up.</preset>
            <preset onclick="preset(4, this)">Reset Filters</preset>
        </presets>

        <h3>Fatalities in Nigeria</h3>

        <div style="float:right">
            <h3>Days of the Week</h3>
            <div class="chart" id="dow"></div>
            <h3>Types of Event</h3>
            <div class="chart" id="e_type"></div>
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
var e_timeline = new smartbox("timeline", 800, 150);
var e_dow = new smartbox("dow", 200, 300);
var e_type = new smartbox("e_type", 300, 400);
var e_boko = new smartbox("boko", 100, 80);

e_timeline.setData(json_data.rows, "y", "f");
e_dow.setData(json_data.rows, "d", "f");
e_type.setData(json_data.rows, "e", "f");
e_map.setData(json_data.rows, "place_name", "f");
e_boko.setData(json_data.rows, "b", "f");

function join_filters() {
    reset_menu();

    e_timeline.applyFilters(filters);
    e_dow.applyFilters(filters);
    e_type.applyFilters(filters);
    e_map.applyFilters(filters);
    e_boko.applyFilters(filters);

    e_timeline.doDraw();
    e_dow.doDraw();
    e_type.doDraw();
    e_map.doDraw();
    e_boko.doDraw();
}

e_timeline.setFilterEvent( join_filters );
e_dow.setFilterEvent( join_filters );
e_type.setFilterEvent( join_filters );
e_map.setFilterEvent( join_filters );
e_boko.setFilterEvent( join_filters );

e_timeline
    .setMargin("all", 10)
    .doDraw();

e_dow
    .setMargin("all", 10)
    .setVertical()
    .setAnchorTop()
    .doDraw();

e_type
    .setMargin("all", 10)
    .setVertical()
    .setAnchorTop()
    .doDraw();

// hardcoded temporarily
var f = { "text-anchor": "start" }
for( var i = 0; i < 9; i++ ) {
    var temp_key = "b" + i + "_label2";
    e_type.e[temp_key].attr(f).attr({ text: json_data.keys.shorten.EVENT_TYPE[i+1] });
}

e_map
    .doDraw();

e_boko
    .setVertical()
    .setAnchorTop()
    .doDraw();


function reset_menu() {
    var presets = document.querySelectorAll("preset");
    for( var i = 0; i < presets.length; i++ ) {
        presets[i].className = "";
    }
}

function preset(p, that) {
//    reset_menu();

    switch( p ) {
        case 1: filters = { place_name: { key: "place_name", value: "borno" } }; break;
        case 2: filters = { d: { key: "d", value: "F" } }; break;
        case 3: filters = { b: { key: "b", value: "Y" } }; break;
        case 4: filters = {}; break;
    }

    join_filters();
    that.className = "active";

}


//timeline.drawMargins();

</script>
</body>
</html>
