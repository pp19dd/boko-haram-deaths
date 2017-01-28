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
    background-color: #AAAAAA;
    padding:0.25em;
    color: #111;
    font-family: 'Roboto', sans-serif;
}
content { padding: 1em; display: block }
.chart { margin-bottom: 1em }
#timeline { width: 800px; height:150px; }
#fatalities { width: 800px; height:150px; }
#map  { width: 800px; height:600px; }
#e_type { width: 300px; height:400px; }
#dow { width: 200px; height:300px; }

#bh { width: 300px; height:300px;  }

svg tspan, svg text { pointer-events: none }
</style>
<script src="raphael.min.js"></script>
<script src="rainbowvis.js"></script>
<script src="plot.js?ts=<?php echo time() ?>"></script>
<script src="plot-map.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content>
        <h3>Fatalities In Nigeria</h3>

        <div style="float:right">
            <h3>Days of The Week</h3>
            <div class="chart" id="dow"></div>
            <h3>Types of Event</h3>
            <div class="chart" id="e_type"></div>
        </div>
        <div class="chart" id="timeline"></div>
        <div class="chart" id="map" style="float:left"></div>

<!--
        <div class="chart" id="bh"></div>
-->
    </content>
    <div id="chart"></div>
<script>

var json_data = <?php readfile("data-clean.json"); ?>;
var json_map = <?php readfile("map.json"); ?>;

var e_map = new smartmap("map", 800, 600);
//e_map.init();
//console.info( typeof e_map );

e_map.paper.setViewBox(0, 155, 525, 390);
for( var state in json_map )(function(key, path) {
    e_map.addPlace(key, path);
})(state, json_map[state]);

// ===========================================================================
// incidents over time
// ===========================================================================
var e_timeline = new smartbox("timeline", 800, 150);
var e_dow = new smartbox("dow", 200, 300);
var e_type = new smartbox("e_type", 300, 400);

var fatalities_by_state = {};

for( var i = 0; i < json_data.rows.length; i ++ ) {
    // if( data.rows[i].b == "N" ) continue;

    if( typeof fatalities_by_state[json_data.rows[i].l] == "undefined" ) {
        fatalities_by_state[json_data.rows[i].l] = 0;
    }
    fatalities_by_state[json_data.rows[i].l] += parseFloat(json_data.rows[i].f);

    e_timeline.addData(json_data.rows[i].y, json_data.rows[i].f);
    e_dow.addData(json_data.rows[i].d, json_data.rows[i].f);
    e_type.addData(json_data.rows[i].e, json_data.rows[i].f);

    var place_name = json_data.keys.shorten.ADMIN1[json_data.rows[i].l];
    place_name = place_name.toLowerCase();
    place_name = place_name.split(" ").join("_");
    e_map.addData(place_name, json_data.rows[i].f);
}

// console.dir( fatalities_by_state );
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

e_map
    .doDraw();

//timeline.drawMargins();

</script>
</body>
</html>
