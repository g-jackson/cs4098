<html>
<head>
        <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

<?php
$pid = @$_GET[pid];
$file = @$_POST[pml];

exec('./peos -l '.$pid.' -c pml/'.$file.".pml 2>&1", $results);
for ($i = 0; $i < sizeof($results); $i++)
	echo $results[$i]."<br>";

echo "You will be redirected to the Patient Pathway List shortly.";

header('Refresh: 1;url=../pathways.php?pid='.$pid);

?>

<br>
<br>
<center>
<a href="../pathways.php?pid=<?php echo $pid ?>">Patient Pathway List</a>
</center>

</body>
</html>
