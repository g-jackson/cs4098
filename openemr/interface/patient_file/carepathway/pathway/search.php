<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

<html>
<head>
        <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<br>
	<form action="../pathways.php" method="get">
		Patient ID: <input name="pid" type="text"></input>
		<input type="submit">
	</form>
</body>
</html>
