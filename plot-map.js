
var smartmap = function(id, w, h) {
    this.init(id, w, h);
    this.e = {}
}

smartmap.prototype = smartbox.prototype;
smartmap.prototype.constructor = smartmap;

smartmap.prototype.addPlace = function(k, path) {
    this.e[k] = this.paper.path(path);

}
