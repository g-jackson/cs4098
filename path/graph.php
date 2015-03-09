<!DOCTYPE html>
<meta charset="utf-8">
<style>

#pathview {
	position: relative;
	width: 960px;
	height: 540px;
}

#selected_action {
	background-color: white;
	position: absolute;
	top: 10px;
	right: 10px;
	width: 200px;
	height: 200px;
	overflow: scroll;
	padding: 5px;
	visibility: hidden;
}

.node {
	stroke: #fff;
	stroke-width: 1.5px;
}

.link {
	stroke: #888;
	stroke-width: 2px;
}

marker#arrow {
    stroke: #888;
    fill: #888;
}

</style>

<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

<body>



<script src="../javascripts/d3.min.js"></script>
<script src="../javascripts/xml2json.min.js"></script>

<h1><b>Pathview</b></h1>

<div id="pathview">

	<div id="selected_action" >
		<div id="selected_action_name" style="font-weight:bold" ></div>
		<div id="selected_action_state"></div>
		<br/>
		Script:
		<div id="selected_action_script"></div>
	</div>

	<!-- get pid for passing to js -->
	<div id="dom-target" style="display: none;">
	<?php 
		$pid = $_GET[pid];
		$procid = $_GET[procid];
		echo htmlspecialchars($pid . " " . $procid);
	?>
	</div>

	<script>
	var div = document.getElementById("dom-target");
	var data = div.textContent;
	var data = data.match(/\S+/g);
	file = (data[0]+".dat.xml");
	file = file.replace(/\s/g, '');
	proc = data[1];
	document.write("<br>pid = " + data[0]);
	document.write("<br>process = " + proc);

	var x2js = new X2JS();
	var proc_table_loc = file;
	var proc_table;

	var width = 960,
		height = 540;
	
	var NODE_RADIUS = 8;
	var SELECTED_NODE_RADIUS = 12;

	var process_data;
	var prev_click;

	var links = [];
//	var links = {"links":[
//		{"source":0, "target":3},
//		{"source":1, "target":3}
//	]};
//	links.links.push({"source":"2", "target":"3"});

	function get_state_colour(d) {
		if (d._state == "BLOCKED") {
			return "red";
		} else if (d._state == "AVAILABLE") {
			return "orange";
		} else if (d._state == "READY") {
			return "yellow";							
		} else if (d._state == "COMPLETE") {
			return "green";
		} else {
			return "grey";
		}
	}

	function load_path_data(id) {
		var xmlhttp;
		var txt,x,xx,i;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		} else {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200){

				var xml_string = new XMLSerializer().serializeToString(xmlhttp.responseXML.documentElement);
				proc_table = x2js.xml_str2json(xml_string);

				process_data = proc_table.process_table.process[id];

				force
					.nodes(process_data.action)
					.links(links)
					.start();

				var link = svg.selectAll(".link")
					.data(links)
					.enter().append("line")
					.attr("class", "link")
					.attr("marker-end", "url(#arrow)");

				var node = svg.selectAll(".node")
					.data(process_data.action)
					.enter().append("circle")
					.attr("class", "node")
					.attr("r", NODE_RADIUS)
					.style("fill", function(d) {
						return get_state_colour(d);
					})
					.on("click", function(d) {
						// resize last circle clicked on
						if (typeof(prev_click) != "undefined") {
							d3.select(prev_click).attr("r", NODE_RADIUS);
						}
						d3.select(this).attr("r", SELECTED_NODE_RADIUS);
			
						// activate display box
						document.getElementById("selected_action").style.visibility = "visible";
			
						// display name
						document.getElementById("selected_action_name").innerHTML = d._name;
			
						// display state with colour
						if (d._state == "BLOCKED") {
							document.getElementById("selected_action_state").innerHTML = "Blocked";
							document.getElementById("selected_action_state").style.color = "red";
						} else if (d._state == "AVAILABLE") {
							document.getElementById("selected_action_state").innerHTML = "Available";
							document.getElementById("selected_action_state").style.color = "orange";
						} else if (d._state == "READY") {
							document.getElementById("selected_action_state").innerHTML = "Ready";
							document.getElementById("selected_action_state").style.color = "yellow";
						} else if (d._state == "COMPLETE") {
							document.getElementById("selected_action_state").innerHTML = "Complete";
							document.getElementById("selected_action_state").style.color = "green";
						} else {
							document.getElementById("selected_action_state").innerHTML = "Undefined";
							document.getElementById("selected_action_state").style.color = "grey";
						}
			
						// display script
						document.getElementById("selected_action_script").innerHTML = d.script;

						// clear previously listed resources
						var resources = document.getElementById("req_resources");
						if (resources != null) {
							resources.parentNode.removeChild(resources);
						}
						var resources = document.getElementById("prov_resources");
						if (resources != null) {
							resources.parentNode.removeChild(resources);
						}

						// display required resoures
						var resources = document.createElement("div");
						resources.setAttribute("id", "req_resources");
						resources.innerHTML = "<br /> Required Resoures:";
						document.getElementById("selected_action").appendChild(resources);
						if (d.req_resource != null) {
							if (d.req_resource.length >= 2) { // display list of resources
								for (var i = 0; i < d.req_resource.length; i++) {
									var resource = document.createElement("div");
									resource.setAttribute("id", "resource"+i);
									resource.innerHTML = d.req_resource[i]._name;
									resources.appendChild(resource);
								}
							} else { // display only one resource
								var resource = document.createElement("div");
								resource.setAttribute("id", "resource");
								resource.innerHTML = d.req_resource._name;
								resources.appendChild(resource);								
							}
						}

						// display provided resoures
						var resources = document.createElement("div");
						resources.setAttribute("id", "prov_resources");
						resources.innerHTML = "<br /> Provided Resoures:";
						document.getElementById("selected_action").appendChild(resources);
						if (d.prov_resource != null) {
							if (d.prov_resource.length >= 2) { // display list of resources
								for (var i = 0; i < d.prov_resource.length; i++) {
									var resource = document.createElement("div");
									resource.setAttribute("id", "resource"+i);
									resource.innerHTML = d.prov_resource[i]._name;
									resources.appendChild(resource);
								}
							} else { // display only one resource
								var resource = document.createElement("div");
								resource.setAttribute("id", "resource");
								resource.innerHTML = d.prov_resource._name;
								resources.appendChild(resource);								
							}
						}

						prev_click = this;
					})
					.call(force.drag);

				node.append("title")
				  .text(function(d) { return d._name; });

				force.on("tick", function() {
					link.attr("x1", function(d) { return d.source.x; })
						.attr("y1", function(d) { return d.source.y; })
						.attr("x2", function(d) { return d.target.x; })
						.attr("y2", function(d) { return d.target.y; });
					node.attr("cx", function(d) { return d.x; })
						.attr("cy", function(d) { return d.y; });
				});
			}
		}
		xmlhttp.open("GET",proc_table_loc,true);
		xmlhttp.send();
	}

	load_path_data(proc);

	var force = d3.layout.force()
		.charge(-120)
		.linkDistance(50)
		.size([width, height]);

	var svg = d3.select("#pathview").append("svg")
		.attr("width", width)
		.attr("height", height);

	// draw grey background
	svg.append("rect")
		.attr("width", "100%")
		.attr("height", "100%")
		.attr("fill", "lightgrey")
		.on("click", function() {
			// resize last circle clicked on
			if (typeof(prev_click) != "undefined") {
				d3.select(prev_click).attr("r", NODE_RADIUS);
				prev_click = null;
			}
			
			// clear and hide selected action box
			var resources = document.getElementById("resources");
			if (resources != null) {
				resources.parentNode.removeChild(resources);
			}
			document.getElementById("selected_action").style.visibility = "hidden";
		});
	</script>

	<svg id="something_important_for_arrows">
		<defs>
			<marker id="arrow" viewbox="0 -5 10 10" refX="18" refY="0" markerWidth="6" markerHeight="6" orient="auto">
				<path d="M0,-5L10,0L0,5Z">
			</marker>
	   </defs>
	</svg>
	
</div>

</body>
