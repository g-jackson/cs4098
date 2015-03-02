<html>
<body>

<?php
$file = @$_POST[pml];
echo $file;

//$arr = scandir("../");
//for ($i = 0; $i < sizeof($arr); $i++)
//	echo $arr[$i];

$results = shell_exec('../peos/os/kernel/peos -c pml/'.$file.".pml");
echo $results;
?>

</body>
</html>
