
function smartbox(id, w, h) {
    this.init(id, w, h);
}

// thx S/O # 2901298
smartbox.prototype.num = function(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

smartbox.prototype.setAnchorTop = function() {
    this.anchor_top = true;
    return( this );
}

smartbox.prototype.setMargin = function(k, a) {
    this.margin[k] = a;
    return( this );
}

smartbox.prototype.setAnchorBottom = function() {
    this.anchor_top = false
    return( this );
}

smartbox.prototype.setHorizontal = function() {
    this.horizontal = true;
    return( this );
}

smartbox.prototype.setVertical = function() {
    this.horizontal = false;

    var temp = this.w;
    this.w = this.h;
    this.h = temp;

    return( this );
}

// used for debugging geometry
smartbox.prototype.drawMargins = function() {
    var x = this.margin.all + this.margin.left;
    var y = this.margin.all + this.margin.top;
    var w = this.w - this.margin.all - this.margin.right - x;
    var h = this.h - this.margin.all - this.margin.bottom - y;

    this.paper.rect(x, y, w, h).attr({
        fill: "yellow", opacity: 0.3
    });
    return( this );
}

smartbox.prototype.init = function(id, w, h) {
    this.paper = null;

    this.filters = [];
    this.id = id;
    this.w = w;
    this.h = h;

    this.e = {};

    this.horizontal = true;
    this.anchor_top = false;

    this.range = {
        min: -Infinity,
        max: -Infinity,
        length: 0
    }

    this.data = {};

    this.margin = {
        all: 5,
        spacing: 5,
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        label_height: 20
    }

    this.style = {
        animation_time: 300,
        bar: { fill: '#7FDBFF' },
        trigger: { fill: 'white', opacity: 0, cursor: 'pointer'},
        text: {
            value: { "font-family": "Roboto", "font-size": 14, fill: "#dddddd" },
            label: { "font-family": "Roboto", "font-size": 14, fill: "#aaaaaa" },
            over: { fill: "orange" },
            vertical: {
                value: { "text-anchor": "end" },
                label: {  }
            }
        }
    }

    this.paper = Raphael(this.id, this.w, this.h);

    this.rainbow = new Rainbow();
    this.rainbow.setSpectrum("#333333", "#FF851B", "#ff4136");
    this.setHorizontal();
    this.setAnchorBottom();
}

smartbox.prototype.applyFilters = function(filters) {
    this.filters = filters;

    this.data = {};
    this.range = {
        min: -Infinity,
        max: -Infinity,
        length: 0
    }

    for( var i = 0; i < this.original_data.length; i++ ) {
        this.addData(
            this.original_data[i],
            this.original_data_key,
            this.original_data[i][this.original_data_mag]
        );
    }
}

smartbox.prototype.setData = function(data_rows, data_key, data_mag) {
    this.original_data = data_rows;
    this.original_data_key = data_key;
    this.original_data_mag = data_mag;

    this.applyFilters([]);
}

smartbox.prototype.rowHide = function(data_row) {
    for( var fk in filters ) {
    //for( var i = 0; i < this.filters.length; i++ ) {
        var ck = filters[fk].key;
        var cv = filters[fk].value;

        if( data_row[ck] != cv ) return( true );
    }
    return( false );
}

smartbox.prototype.addData = function(data_row, data_key, magnitude) {

    var k = data_row[data_key];

    if( typeof this.data[k] == "undefined" ) {
        this.data[k] = 0;
        this.range.length++;
    }

    if( this.rowHide(data_row) == false ) {
        if( typeof magnitude == 'undefined') {
            this.data[k]++;
        } else {
            this.data[k] += parseFloat(magnitude);
        }
    }

    //if( this.rowFiltered(data_row, data_key)
    // var count_filtered = 0;
    // for( f = 0; f < this.filters.length; f++ ) {
    //     count_filtered += this.filters[f](data_row, data_key);
    // }

    // if( count_filtered == 0 ) {
    //     if( typeof magnitude == 'undefined') {
    //         this.data[k]++;
    //     } else {
    //         this.data[k] += parseFloat(magnitude);
    //     }
    // }

    if( -this.data[k] > this.range.min ) this.range.min = this.data[k];
    if( this.data[k] > this.range.max ) this.range.max = this.data[k];

    return( this );
}

smartbox.prototype.getChunkX = function() {
    var margins = (this.margin.all*2) + this.margin.left + this.margin.right;

    var available = this.w - margins + this.margin.spacing;
    var chunk = available / this.range.length;

    return( chunk );
}

smartbox.prototype.getX = function(i) {
    var chunk = this.getChunkX();
    var pos = this.margin.all + this.margin.left + (i * chunk);

    //var cumulative_spacings = (this.range.length - 1) * this.margin.spacing;
    //var partial_spacing = i * (cumulative_spacings / this.range.length-5);
    //return( pos + partial_spacing );
    return( pos );
}

smartbox.prototype.getChunkY = function() {
    var margins = (this.margin.all*2) + this.margin.top + this.margin.bottom + this.margin.label_height;

    var available = (this.h - margins);
    var chunk = available / this.range.max;
    return( chunk );
}

smartbox.prototype.getY = function(dat) {
    var chunk = this.getChunkY();
    var pos = (dat * chunk);
    return( pos );
}

smartbox.prototype.XY = function(nx, ny, nw, nh) {
    if( this.horizontal === false ) {
        return({ x: ny, y: nx, w: nh, h: nw });
    } else {
        return({ x: nx, y: ny, w: nw, h: nh });
    }
}

smartbox.prototype.doDraw = function() {
    var tw = this.getChunkX() - this.margin.spacing;

    try {
        this.rainbow.setNumberRange(this.range.min, this.range.max);
    } catch( e ) {

    }

    var index = 0;
    for( var k in this.data ) {//}(function(k, v, i) {
        this.drawBar(k, this.data[k], index, tw);
        index++;
    };

    return( this );
}

smartbox.prototype.getGeometry = function(k, v, i, tw) {
    var x = this.getX(i);
    var h = this.getY(v);

    var th = this.h;

    if( this.anchor_top ) {
        var y = this.margin.all + this.margin.top + this.margin.label_height;

        // key label
        var ky = (this.margin.all) + this.margin.top + 10;

        // value label positions
        var vx = x + (tw / 2);
        var vy = y + h + 10;

        if( vy > (this.h - this.margin.bottom - this.margin.all) ) {
            vy = y + h - 10;
        }
    } else {
        var y = this.h - this.margin.bottom - this.margin.label_height - this.margin.all - h;

        // key label
        var ky = this.h - (this.margin.all) - this.margin.bottom - 10;

        // value label positions
        var vx = x + (tw / 2);
        var vy = y - 10;// - this.margin.label_height;

        if( vy < (this.margin.all + this.margin.top + 10) ) {
            vy = y + 10;
        }
    }

    try {
        var color = "#" + this.rainbow.colourAt(v);
    } catch( err ) {
        var color = "#ffffff";
    }

    return({
        k: k,
        v: v,
        i: i,
        tw: tw,
        T1: this.XY(x, y, tw, h),
        T2: this.XY( vx, vy, 0, 0 ),
        T3: this.XY( vx, ky, 0, 0 ),
        T4: this.XY( x, this.margin.all + this.margin.top, tw, th ),
        fill: color
    });
}

smartbox.prototype.setFilterEvent = function(f) {
    this.trigger_filter_events = f;
}

smartbox.prototype.drawBarFirst = function(g) {
    var that = this;

    var r = this.paper.rect( g.T1.x, g.T1.y, g.T1.w, g.T1.h ).attr( this.style.bar );
    var l1 = this.paper.text( g.T2.x, g.T2.y, this.num(g.v) ).attr( this.style.text.value );
    var l2 = this.paper.text( g.T3.x, g.T3.y, g.k ).attr( this.style.text.label );

    r.attr({ fill: g.fill });

    if( this.horizontal === false ) {
        l1.attr(this.style.text.vertical.value);
        l2.attr(this.style.text.vertical.label);
    }

    var trigger = this.paper.rect(
        g.T4.x, g.T4.y, g.T4.w, g.T4.h
    );
    trigger.attr( this.style.trigger );

    trigger.mouseover(function() {
        //if( this.status().length > 0 ) return;
        // l1.show();
        r.stop().animate({transform: "s1.2", opacity: 0.5 }, that.style.animation_time, "<>");
        l2.attr(that.style.text.over);
    }).mouseout(function() {
        // l1.hide();
        //if( this.status().length > 0 ) return;
        r.stop().animate({transform: "r0", opacity: 1 }, that.style.animation_time, "<>");
        l2.attr(that.style.text.label);
    }).click(function() {
        var filter_id = that.original_data_key;
        if( typeof filters[filter_id] == "undefined" ) {
            filters[filter_id] = {
                key: that.original_data_key,
                value: g.k
            };
        } else {
            // selecting a different bar, or toggling same one?
            if( filters[filter_id].value == g.k ) {
                delete filters[filter_id];
            } else {
                filters[filter_id].value = g.k;
            }
        }
        that.trigger_filter_events();
    });

    return({
        r: r, l1: l1, l2: l2, trigger: trigger
    });
}

smartbox.prototype.drawBar = function(k, v, i, tw) {
    var g = this.getGeometry(k, v, i, tw);

    var prefix = "b" + i + "_";
    if( typeof this.e[prefix + "bar"] == "undefined" ) {
        var bar = this.drawBarFirst(g);

        this.e[prefix + "bar"] = bar.r;
        this.e[prefix + "label1"] = bar.l1;
        this.e[prefix + "label2"] = bar.l2;
        this.e[prefix + "geometry"] = g;
    } else {

        if( this.horizontal === false ) {
            this.e[prefix + "bar"].stop().animate({
                width: g.T1.w,
                fill: g.fill
            }, this.style.animation_time, "<>");
        } else {
            this.e[prefix + "bar"].stop().animate({
                y: g.T1.y,
                height: g.T1.h,
                fill: g.fill
            }, this.style.animation_time, "<>");
        }

        this.e[prefix + "label1"].attr({
            "text": this.num(g.v)
        }).stop().animate({
            x: g.T2.x,
            y: g.T2.y
        }, this.style.animation_time, "<>");
        //var l1 = this.paper.text( g.T2.x, g.T2.y, this.num(g.v) ).attr( this.style.text.value );


    }


    return( this );
}














// ...
