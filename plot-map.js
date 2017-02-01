
function smartmap(id, w, h) {
    this.init(id, w, h);

    this.tooltip = this.paper.text(1,1, "LOL");
    this.tooltip.attr({
        fill: "white"
    })
}

smartmap.prototype = Object.create(smartbox.prototype);
smartmap.prototype.constructor = smartmap;

smartmap.prototype.addPlace = function(k, path) {
    var p = this.paper.path(path);
    var that = this;
    p.attr({ fill: "white", cursor: "pointer" });
    p.mouseover(function() {
        // if( this.status().length > 0 ) return;
        this.stop().toFront().animate({ opacity: 0.3}, that.style.animation_time, "<>");
    }).mouseout(function() {
        // if( this.status().length > 0 ) return;
        this.stop().toFront().animate({ opacity: 1}, that.style.animation_time, "<>");
    }).mousemove(function(e) {
        //console.info( e.clientX, e.clientY );
        that.tooltip.toFront().attr({
            x: e.clientX- 150,
            y: e.clientY- 200,
            text: k
        });
    }).click(function() {
        var filter_id = that.original_data_key;
        if( typeof filters[filter_id] == "undefined" ) {
            filters[filter_id] = {
                key: that.original_data_key,
                value: k
            };
        } else {
            // selecting a different bar, or toggling same one?
            if( filters[filter_id].value == k ) {
                delete filters[filter_id];
            } else {
                filters[filter_id].value = k;
            }
        }
        that.trigger_filter_events();
    });

    this.e[k] = p;
}

smartmap.prototype.doDraw = function() {
    // var p = this.paper.path(path);
    var that = this;

    try {
        this.rainbow.setNumberRange(this.range.min, this.range.max);
    } catch( e ) {

    }

    for( k in this.e )(function(k, e) {
        if( typeof that.data[k] == "undefined" ) return;

        try {
            var fill = "#" + that.rainbow.colourAt(that.data[k]);
        } catch( e ) {
            var fill = "#ffffff";
        }

        that.e[k].attr({fill: fill });
    })(k, this.e[k]);

}
