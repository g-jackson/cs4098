<!--this file should have a form for a pml doc that will be saved into the /pml folder in this directory-->
<!--this script imports the navbar from navbar.html-->
<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

Form:
-File Title
-File
-Submit Button

<html>
<head>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
<br>
<br>
	<form action="addpathsubmit.php" method="post" enctype="multipart/form-data">
		File Title: <input name="title" type="text"></input>
		<br>
		File: <input name="file" type="file"></input>
		<br>
		<input type="submit">
	</form>
</body>
</html>
