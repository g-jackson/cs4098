<style>
li {
    display: inline;
}
ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
}
</style>
<ul>
  <li><a href="pathway/patient_table.php">Home</a> |</li>
  <li><a href="pathway/addpath.php">Add .pml</a> |</li>
  <li><a href="pathway/addprocess.php">Add Pathway</a> |</li>
  <li><a href="pathway/search.php">Search For Patient</a></li>
</ul>

<html>
  <head>
    <title>Pathview</title>
    <link rel="stylesheet" type="text/css" href="pathway/main.css">
  </head>
  <body>

  <style>
    /*table, tr, td
    {
      border: 1px solid black;
        border-collapse: collapse;
    }*/
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
    var file = "pathway/";
    file = file.concat(pid.concat(".dat.xml"));
    file = file.replace(/\s/g, '');
    //document.write("Path ="+ file);
    document.write("Patient ID ="+ pid);
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

    document.write("<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><th>\
      Process ID</th><th>\
      Model</th><th>\
      Status</th><th>\
      Graph</th></tr>");
    var x = xmlDoc.getElementsByTagName("process");
    var row_count = 0;
    for (i = 0; i < x.length; i++)
      {
      //document.write("<tr><td>");
      if (row_count%2 == 0) document.write("<tr class=\"odd\"><td>");
      else if (row_count%2 == 1) document.write("<tr class=\"even\"><td>");
      row_count++;
      document.write(x[i].getAttribute("pid"));
      document.write("</td><td>");
      document.write(x[i].getAttribute("model"));
      document.write("</td><td>");
      document.write(x[i].getAttribute("status"));
      document.write("</td><td>");
      var graphlink = "graph.php?pid=" + pid + '&' + "procid=" + i;
      graphlink = graphlink.replace(/\s/g, '');
      document.write("<a href=pathway/"+graphlink+">Pathway</a>");
      document.write("</td></tr>");
      }
    document.write("</table>");
  </script>
  </body>
</html>
