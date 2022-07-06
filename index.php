<html>
	<head><title>Mikrotik Exporter</title></head>
	<body>
		<h1>Mikrotik Exporter</h1>
		<p>To view device-metrics use URL like<br /><br />
		<code><?=$_SERVER['REQUEST_SCHEME']?>://<?=$_SERVER['HTTP_HOST'].str_replace('index.php','metrics',$_SERVER['DOCUMENT_URI'])?>/192.168.1.1</code><br />
		...where <code>192.168.1.1</code> - target device.<br />
		Don't forget to add it to <code>db.yml</code><br /><br />
                Example link above may be different if you are using Docker - generate it by yourself<br /><br />
		<p>Visit <a href="https://github.com/D13410N3/mikrotik_prometheus_exporter" target="_blank">Github</a> to get more info</p>
	</body>
</html>
