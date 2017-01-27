<!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<style>
body, html { height: 100%; width: 100%; margin:0; padding:0; }
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
<script src="plot.js?ts=<?php echo time() ?>"></script>
</head>
<body>
    <content>
        <h3>Incidents Over Time</h3>

        <div style="float:right">
            <h3>Days of The Week</h3>
            <div class="chart" id="dow"></div>
            <h3>Types of Event</h3>
            <div class="chart" id="e_type"></div>
        </div>
        <div class="chart" id="timeline"></div>
        <div class="chart" id="fatalities"></div>
        <div class="chart" id="map" style="float:left"></div>

<!--
        <div class="chart" id="bh"></div>
-->
    </content>
    <div id="chart"></div>
<script>
var data = <?php readfile("data-clean.json"); ?>;
var map = <?php readfile("map.json"); ?>;

// ===========================================================================
// draw map
// ===========================================================================
var paper_map = Raphael("map", 800, 600);
paper_map.setViewBox(0, 155, 525, 390);
for( var state in map )(function(key, path) {
    var s = paper_map.path( path );
    s.attr({
        //fill: "#85144b",
        "stroke-width": "1px",
        "stroke-opacity": "0.9"
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
var timeline = new smartbox("timeline", 800, 150);
var fatalities = new smartbox("fatalities", 800, 150);
fatalities.setAnchorTop();
var dow = new smartbox("dow", 200, 300);
var e_type = new smartbox("e_type", 300, 400);

var fatalities_by_year = {};

for( var i = 0; i < data.rows.length; i ++ ) {
    if( data.rows[i].b == "N" ) continue;


    //var year = data.rows[i].y;
    // if( typeof fatalities_by_year[year] == 'undefined' ) fatalities_by_year[year] = 0;
    // fatalities_by_year[year] += parseInt(data.rows[i].f);

    fatalities.addData(data.rows[i].y, data.rows[i].f);

    dow.addData(data.rows[i].d, data.rows[i].f);
    timeline.addData(data.rows[i].y);
    e_type.addData(data.rows[i].e, data.rows[i].f);
}

//timeline.setData( data.YEAR );
//timeline.setData( data.FATALITIES );
timeline.margin.all = 10;
timeline.doDraw();

fatalities.margin.all = 10;
fatalities.doDraw();

dow.margin.all = 10;
dow.setVertical();
dow.setAnchorTop();
dow.doDraw();

e_type.margin.all = 10;
e_type.setVertical();
e_type.setAnchorTop();
e_type.doDraw();

//timeline.drawMargins();

/*
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
*/
</script>
</body>
</html>
