<!doctype html>
<html>
<head>
<title>iframe test</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0" />
<style>
iframe, h1 { width: 80%; margin-left: 10% }
</style>
</head>
<body>
    <h1>iframe test</h1>

<div id="interactive-map"></div>
<script type="text/javascript" src="pym.v1.min.js"></script>
<script>
var pymParent = new pym.Parent('interactive-map', 'index.php', {});
</script>

</body>
</html>
