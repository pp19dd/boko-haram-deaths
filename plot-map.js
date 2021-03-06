
function smartmap(id, w, h) {
    this.init(id, w, h);

    this.tooltip = this.paper.text(1,1, "test");
    this.tooltip.attr({
        fill: "white",
        opacity: 0
    })
}

smartmap.prototype = Object.create(smartbox.prototype);
smartmap.prototype.constructor = smartmap;

smartmap.prototype.addPlace = function(k, path) {
    var p = this.paper.path(path);
    var that = this;
    p.attr({ fill: "black", cursor: "pointer" });
    p.mouseover(function(e) {
        if( this.__disable == true ) return;

        this.stop().animate({ opacity: 0.3}, that.style.animation_time, "<>");
        //that.tooltip.show();
        var b = this.getBBox();

        if( parseInt(b.cx) < parseInt(that.w / 2)) {
            that.tooltip.attr({ "text-anchor": "start"});
        } else {
            that.tooltip.attr({ "text-anchor": "end"});
        }

        var n = k.split("_");
        for( var i = 0; i < n.length; i++ ) {
            n[i] = n[i].substr(0,1).toUpperCase() + n[i].substr(1);
        }

        var text = n.join(" ");
        if( that.data[k] > 0 ) {
            text += ": " + that.num(that.data[k]) + " Fatalities";
        }

        // write text, then measure its box
        var tx = b.cx;
        var ty = b.cy;
        //that.tooltip.toFront().attr({ opacity: 0, text: text });
        that.tooltip.toFront().attr({ text: text });
        var b2 = that.tooltip.getBBox();

        // apply any adjustments so text doesn't clip
        //if( b2.x < 0 ) tx += 10 -b2.x;

        // show tooltip
        that.tooltip.attr({
            x: tx,
            y: ty
        });

        that.tooltip.stop().animate({ opacity: 1 }, that.style.animation_time, "<>");
//        that.tooltip.animate({
//            opacity: 1,
//        }, that.style.animation_time, "<>");
//


    }).mouseout(function() {
        if( this.__disable == true ) return;

        this.stop().animate({ opacity: 1}, that.style.animation_time, "<>");
        that.tooltip.stop().animate({ opacity: 0 }, that.style.animation_time, "<>");
        //that.tooltip.hide();
    }).click(function() {
        if( this.__disable == true ) return;

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

    p.__disable = false;
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
