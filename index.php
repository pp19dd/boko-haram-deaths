<?php
require("data.php");
?><!doctype html>
<html>
<head>
<title>Boko Haram Deaths</title>
<style>
body, html { height: 100%; width: 100%; margin:0; padding:0; }
content { padding: 1em; display: block }
svg#timeline { width: 800px; height:200px; background-color: silver }
</style>
<script src="https://d3js.org/d3.v4.min.js"></script>
</head>
<body>
    <content>
        <h3>Boko Haram Deaths</h3>

        <svg id="timeline"></svg>
    </content>
    <div id="chart"></div>
<script>
var data = <?php echo json_encode($unique); ?>;

function ok_to_a(o) {
    var n = [];
    for( var k in o ) {
        n.push(k);
    }
    return( n );
}

function ov_to_a(o) {
    var n = [];
    for( var k in o ) {
        n.push(o[k]);
    }
    return( n );
}

var x = d3.scaleLinear()
    .domain([0, ov_to_a(data.YEAR.length)])
    .range([0, 800]);

var y = d3.scaleLinear()
    .domain([0, d3.max(ov_to_a(data.YEAR))])
    .range([0,200]);

d3
    .select("svg#timeline")
    .selectAll("rect")
    .data(ov_to_a(data.YEAR))
    .enter()
    .append("rect")
    .attr("height", function(d) { return( y(d) ); })
    .attr("width", function(d) { return( 5 ); })
    .attr("x", function(d) { return( x(d) ); })
    ;


//d3.select("body")
/*  .append("svg")
    .attr("width", 960)
    .attr("height", 500)
  .append("g")
    .attr("transform", "translate(20,20)")
  .append("rect")
    .attr("width", 920)
    .attr("height", 460);*/


/*
var r = d3.scale.linear()
    .domain([0, d3.max(data.YEAR)])
    .range([0, 800]);
*/
/*
d3.select("svg#timeline")
    .data(data.YEAR)
    .enter()
    .append("rect")
    .attr("width", x)
    .attr("height", 10)
    .attr("x", 40)
    .attr("y", 40)
*/


/*
var data = ov_to_a(data.YEAR);

var timeline = d3.select("svg#timeline");

var bar = timeline.selectAll("g");

var barUpdate = bar.data(data);

var barEnter = barUpdate.enter().append("circle");
*/

/*
    .append("g")
    .selectAll("g")
    .data(ov_to_a(data.YEAR))
    .enter()
    .append("text")
    .text(function(d) {
        return(d);
    })
;
*/
    /*
g
    .data(ov_to_a(data.YEAR))
    .enter()
    .append("g");

    .append("rect")
    .attr("r", function(d) {
        return( "WAT " + d);
    });
    */
    /*
    .data(o_to_a(data.YEAR))
    .enter()
    .append("rect")
    .text("-1")
    ;
*/


//3.selectAll("h3").style("color", "white");/
</script>
</body>
</html>
