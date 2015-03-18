<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

<html>
<body>

<?php
$pid = @$_POST[ID];
$file = @$_POST[pml];
//$arr = scandir("../");
//for ($i = 0; $i < sizeof($arr); $i++)
//	echo $arr[$i];

exec('./peos -l '.$pid.' -c pml/'.$file.".pml 2>&1", $results);
//exec('../peos/os/kernel/peos -i 2>&1', $results);
//echo sizeof($results);
for ($i = 0; $i < sizeof($results); $i++)
	echo $results[$i];


?>

</body>
</html>
