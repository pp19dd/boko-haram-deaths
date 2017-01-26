<!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<style>
body, html { height: 100%; width: 100%; margin:0; padding:0; }
content { padding: 1em; display: block }
.chart { margin-bottom: 1em }
#timeline { width: 800px; height:200px; background-color: silver }
#map  { width: 800px; height:600px; background-color: silver }
#bh { width: 300px; height:300px; background-color: silver }
#e_type { width: 400px; height:600px; background-color: silver }
#dow { width: 300px; height:600px; background-color: silver }
#fatalities { width: 800px; height:600px; background-color: silver }
svg tspan, svg text { pointer-events: none }
</style>
<script src="raphael.min.js"></script>
<script src="plot.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content>
        <h3>Boko Haram Deaths</h3>

        <div class="chart" id="timeline"></div>
        <div class="chart" id="e_type" style="float:right"></div>
        <div class="chart" id="bh"></div>
        <div class="chart" id="map"></div>
        <div class="chart" id="dow"></div>
        <div class="chart" id="fatalities"></div>
    </content>
    <div id="chart"></div>
<script>
var data = <?php include("data.php" ); ?>;
var map = <?php readfile("map.json"); ?>;

// ===========================================================================
// draw map
// ===========================================================================
var paper_map = Raphael("map", 800, 600);
paper_map.setViewBox(0, 155, 525, 390);
for( var state in map )(function(key, path) {
    var s = paper_map.path( path );
    s.attr({
        fill: "#85144b",
        "stroke-width": "4px",
        "stroke-opacity": "0.2"
    });
    s.mouseover(function() {
        this.stop().toFront().animate({ opacity: 0.7}, 300, "<>");
        console.info( key );
    });
    s.mouseout(function() {
        this.stop().toFront().animate({ opacity: 1}, 300, "<>");
    });
})(state, map[state]);

// ===========================================================================
// incidents over time
// ===========================================================================
var timeline = new smartbox("timeline", 800, 200);
timeline.setData( data.YEAR );
//timeline.setData( data.FATALITIES );
//timeline.margin.all = 10;
timeline.doDraw();
timeline.drawMargins();

var bh = new smartbox("bh", 300, 300 );
bh.setData( data.BOKO_HARAM );
bh.doDraw();


var e_type = new smartbox("e_type", 400, 600 );
e_type.setVertical();
e_type.setAnchorTop();
e_type.margin.all = 25;
e_type.margin.spacing = 30;
e_type.setData( data.EVENT_TYPE );
e_type.doDraw();

var dow = new smartbox("dow", 300, 600 );
dow.setData( data.DAY_OF_WEEK );
dow.doDraw();

var fatalities = new smartbox("fatalities", 800, 600 );
fatalities.setData( data.FATALITIES );
fatalities.margin.all = 25;
fatalities.doDraw();

</script>
</body>
</html>
