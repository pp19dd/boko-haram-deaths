
function smartbox(id, w, h) {
    this.paper = null;

    this.id = id;
    this.w = w;
    this.h = h;

    this.horizontal = true;
    this.anchor_top = false;

    this.range = {
        min: Infinity,
        max: -Infinity,
        length: 0
    }

    this.data = [];

    this.margin = {
        all: 5,
        spacing: 5,
        left: 0,
        right: 0,
        top: 0,
        bottom: 0,
        label_height: 20
    }
// h = 150, is this right?  ..
// 200 - 10 - 10 - 10 - 10 - 12 = no should be 148
    this.style = {
        bar: { fill: '#7FDBFF' },
        text: {
            value: { "font-size": 14, fill: "#111111" },
            label: { "font-size": 14, fill: "#111111" }
        }
    }
    this.init();
}

smartbox.prototype.setAnchorTop = function() {
    this.anchor_top = true;
}

smartbox.prototype.setAnchorBottom = function() {
    this.anchor_top = false
}

smartbox.prototype.setHorizontal = function() {
    this.horizontal = true;
}

smartbox.prototype.setVertical = function() {
    this.horizontal = false;

    var temp = this.w;
    this.w = this.h;
    this.h = temp;
}

// used for debugging geometry
smartbox.prototype.drawMargins = function() {
    var x = this.margin.all + this.margin.left;
    var y = this.margin.all + this.margin.top;
    var w = this.w - this.margin.all - this.margin.right - x;
    var h = this.h - this.margin.all - this.margin.bottom - y;

    // console.info( "all  | width = ", this.w, "height = ", this.h );
    // console.info( "rect | x = ", x, "y = ", y, "w = ", w, "h = ", h );
    // console.dir( this.margin );
    this.paper.rect(x, y, w, h).attr({
        fill: "yellow", opacity: 0.3
    });
}

smartbox.prototype.init = function() {
    this.paper = Raphael(this.id, this.w, this.h);
    this.setHorizontal();
    this.setAnchorBottom();
}

smartbox.prototype.setData = function(d) {
    for( var k in d ) {
        if( d[k] < this.range.min ) this.range.min = d[k];
        if( d[k] > this.range.max ) this.range.max = d[k];

        this.data.push({
            k: k,
            v: d[k]
        });
        this.range.length++;
    }
}

smartbox.prototype.getChunkX = function() {
    var margins = (this.margin.all*2) + this.margin.left + this.margin.right;

    var available = this.w - margins + this.margin.spacing;
    var chunk = available / this.range.length;

    return( chunk );
}

smartbox.prototype.getX = function(i) {
    var chunk = this.getChunkX(i);
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

    for( var i = 0; i < this.range.length; i++ ) {
        var x = this.getX(i);
        var h = this.getY(this.data[i].v);

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

        var T1 = this.XY(x, y, tw, h);
        var T2 = this.XY( vx, vy, 0, 0 );
        var T3 = this.XY( vx, ky, 0, 0 );

        var r = this.paper.rect( T1.x, T1.y, T1.w, T1.h ).attr( this.style.bar );
        this.paper.text( T2.x, T2.y, this.data[i].v ).attr( this.style.text.value );
        this.paper.text( T3.x, T3.y, this.data[i].k ).attr( this.style.text.label );
    }
}












// ...
