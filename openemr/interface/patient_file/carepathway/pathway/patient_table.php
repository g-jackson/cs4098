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
    <title>Pathview</title>
    <link rel="stylesheet" type="text/css" href="main.css">
  </head>
  <body>

  <!--get all xml files -->
  <?php
  $pid;
  foreach(glob('*.dat.xml') as $file) {
      $pid = $pid . ($file ."  ");
  }
  ?>
  <!-- get pid for passing to js -->
  <div id="dom-target" style="display: none;">
    <?php 
        echo htmlspecialchars($pid); 
    ?>
  </div>
  <script> 
    //get pid for js   
    var div = document.getElementById("dom-target");
    var pid = div.textContent;

    var files = pid.match(/\S+/g);
    var row_count = 0;
    /*
    for (i=0;i<files.length;i++){
      document.write(files[i]+"<br>");
    }
    */
    document.write("patients = " +files.length+ "<br>");

    document.write("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><th>\
        Patient ID</th><th>\
        Process ID</th><th>\
        Model</th><th>\
        Status</th><th>\
        Graph</th></tr>");

    for (i =0; i< files.length;i++){
      file = files[i];
      pid = file.match(/\d+/);
      //document.write(file + "<br>");
      
      if (window.XMLHttpRequest){
        xmlhttp = new XMLHttpRequest(); // code for IE7+, Firefox, Chrome, Opera, Safari
      }
      else{
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP"); // code for IE6, IE5
      }

      xmlhttp.open("GET", file ,false);
      xmlhttp.send();
      xmlDoc = xmlhttp.responseXML;

      var x = xmlDoc.getElementsByTagName("process");
      for (j = 0; j < x.length; j++){
        //document.write("<tr><td>");
        if (row_count%2 == 0) document.write("<tr class=\"odd\"><td>");
        else if (row_count%2 == 1) document.write("<tr class=\"even\"><td>");
        row_count++;
        document.write(pid);
        document.write("</td><td>");
        document.write(x[j].getAttribute("pid"));
        document.write("</td><td>");
        document.write(x[j].getAttribute("model"));
        document.write("</td><td>");
        document.write(x[j].getAttribute("status"));
        document.write("</td><td>");
        var graphlink = "graph.php?pid=" + pid + '&' + "procid=" + j;
        graphlink = graphlink.replace(/\s/g, '');
        document.write("<a href="+graphlink+">Pathway</a>");
        document.write("</td></tr>");
      }
  }
  document.write("</table>");
  </script>
  </body>
</html>
