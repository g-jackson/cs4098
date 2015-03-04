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

  <style>
    table, tr, td
    {
      border: 1px solid black;
        border-collapse: collapse;
    }
  </style>

  <!-- get pid for passing to js -->
  <div id="dom-target" style="display: none;">
    <?php 
        $pid = $_GET[pid];
        echo htmlspecialchars($pid); 
    ?>
  </div>

  <script> 
    //get pid for js   
    var div = document.getElementById("dom-target");
    var pid = div.textContent;
    var file = pid.concat(".dat.xml");
    file = file.replace(/\s/g, '');
    //document.write(file);
    if (window.XMLHttpRequest)
      {// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp = new XMLHttpRequest();
      }
    else
      {// code for IE6, IE5
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }

    xmlhttp.open("GET", file ,false);
    xmlhttp.send();
    xmlDoc = xmlhttp.responseXML;

    document.write("<table cellpadding=\"10\"><tr><th>\
      Process ID</th><th>\
      Model</th><th>\
      Status</th><th>\
      Graph</th></tr>");
    var x = xmlDoc.getElementsByTagName("process");
    for (i = 0; i < x.length; i++)
      {
      document.write("<tr><td>");
      document.write(x[i].getAttribute("pid"));
      document.write("</td><td>");
      document.write(x[i].getAttribute("model"));
      document.write("</td><td>");
      document.write(x[i].getAttribute("status"));
      document.write("</td><td>");
      var graphlink = "/graph.php?pid=" + pid + '&' + "procid=" + i;
      graphlink = graphlink.replace(/\s/g, '');
      document.write("<a href="+graphlink+">Pathway</a>");
      document.write("</td></tr>");
      }
    document.write("</table>");
  </script>
  </body>
</html>