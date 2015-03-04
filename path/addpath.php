<!--this file should have a form for a pml doc that will be saved into the /pml folder in this directory-->

Form:
-File Title
-File
-Submit Button

<html>
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
