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

<?php

$title = @$_POST[title];
$file = @$_FILES[file];

$target_dir = "pml/";
$target_file = $target_dir.$title.".pml";
$fileType = pathinfo($file[name],PATHINFO_EXTENSION);

$uploadOk = 1;

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, file already exists.<br>";
    $uploadOk = 0;
}
// Allow certain file formats
if ($fileType != "pml") {
    echo "Sorry, only PML files are allowed.<br>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.<br>";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.<br>";
    } else {
        echo "Sorry, there was an error uploading your file.<br>";
    }
}

/*echo basename($file[name])."<br>";
echo $target_file."<br>";
echo $fileType."<br>";

echo $file[name]."<br>";
echo $file[type]."<br>";
echo $file[size]."<br>";
echo $file[tmp_name]."<br>";
echo $file[error]."<br>";*/


?>

</body>
</html>
