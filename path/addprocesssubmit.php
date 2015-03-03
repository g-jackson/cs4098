<html>
<body>

<?php
$file = @$_POST[pml];
//$arr = scandir("../");
//for ($i = 0; $i < sizeof($arr); $i++)
//	echo $arr[$i];

exec('../peos/os/kernel/peos -c pml/'.$file.".pml 2>&1", $results);
//exec('../peos/os/kernel/peos -i 2>&1', $results);
//echo sizeof($results);
for ($i = 0; $i < sizeof($results); $i++)
	echo $results[$i];


?>

</body>
</html>
