
function smartmap(id, w, h) {
    this.init(id, w, h);
    this.e = {};
}

smartmap.prototype = Object.create(smartbox.prototype);
smartmap.prototype.constructor = smartmap;

// smartmap.prototype = Object.create(smartbox.prototype);
// smartmap.prototype.constructor = smartmap;

//var smartmap = function(id, w, h) {
    // this.init(id, w, h);
    // this.e = {}
//}

smartmap.prototype.addPlace = function(k, path) {
    var p = this.paper.path(path);
    var that = this;
    p.attr({ fill: "white" });
    p.mouseover(function() {
        this.stop().toFront().animate({ opacity: 0.3}, 300, "<>");
        console.info( k, that.data[k] );
    });
    p.mouseout(function() {
        this.stop().toFront().animate({ opacity: 1}, 300, "<>");
    });

    this.e[k] = p;
}

smartmap.prototype.doDraw = function() {
    // var p = this.paper.path(path);
    var that = this;

    for( k in this.e )(function(k, e) {
        if( typeof that.data[k] == "undefined" ) return;

        // console.info( "painting " + k + "value = " + that.data[k]  );
        var fill = "#" + that.rainbow.colourAt(that.data[k]);
        that.e[k].attr({fill: fill });
    })(k, this.e[k]);

}
// smartmap.prototype.doDraw = function() {
//     //console.info( "WAT ");
// }
/*
}
*/
