<!--This file should have a form containing the following
-patient id
-dropdown of .pml files in /pml for selection

When submitted a php script that does the following should happen:
executes ./peos -c pml/"formpml" 
takes the output and finds the process id of the resulting process from it
outputs that process id
-->
<script src="../javascripts/jquery.min.js"></script> 

<?php

$arr = scandir("pml");
$pml_opts = array();
$j = 0;
for ($i = 0; $i < sizeof($arr); $i++) {
	$split = explode(".", $arr[$i]);
	if(sizeof($split) == 2 && $split[1] == "pml") $pml_opts[$j++] = $split[0];
}

$pid = $_GET[pid];

?>

<html>
<head>
        <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
	Patient ID = <?php echo $pid ?>
	<br>
	<br>
	<form action="addprocesssubmit.php?pid=<?php echo $pid ?>" method="post">
		PML:
		<select name="pml"> 
			<?php
				for ($i = 0; $i < sizeof($pml_opts); $i++)
					echo "<option value=\"".$pml_opts[$i]."\">".$pml_opts[$i]."</option>";
			?>
		</select>
		<br>
		<input type="submit">
	</form>

	<center>
	<a href="../pathways.php?pid=<?php echo $pid ?>">Patient Pathway List</a>
	</center>


</body>
</html>
