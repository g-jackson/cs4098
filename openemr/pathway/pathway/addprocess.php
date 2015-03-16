<!--This file should have a form containing the following
-patient id
-dropdown of .pml files in /pml for selection

When submitted a php script that does the following should happen:
executes ./peos -c pml/"formpml" 
takes the output and finds the process id of the resulting process from it
outputs that process id
-->
<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

<?php

$arr = scandir("pml");
$pml_opts = array();
$j = 0;
for ($i = 0; $i < sizeof($arr); $i++) {
	$split = explode(".", $arr[$i]);
	if(sizeof($split) == 2 && $split[1] == "pml") $pml_opts[$j++] = $split[0];
}
?>

<html>
<body>
<br>
	<form action="addprocesssubmit.php" method="post">
		Patient ID: <input name="ID" type="text"></input>
		<br>
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
</body>
</html>
