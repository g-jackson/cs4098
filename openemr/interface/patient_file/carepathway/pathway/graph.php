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


<head>
	<link rel="stylesheet" type="text/css" href="main.css">

	<script src="../javascripts/d3.min.js"></script>
	<script src="../javascripts/xml2json.min.js"></script>

	<script src="xml_action_parsing.js"></script>
</head>

<body>

<!-- get pid for passing to js -->
<div id="dom-target" style="display: none;">
<?php 
	$pid = $_GET['pid'];
	$procid = $_GET['procid'];
	echo htmlspecialchars($pid . " " . $procid);
?>
</div>

<div id="return_link">
<?php 
$retour .= "<a href='../pathways.php?pid=".$pid."'>Back to Patient Pathway List</a>";
echo $retour;
?>
</div>
<br/>


<script>
	var div = document.getElementById("dom-target");
	var data = div.textContent;
	var data = data.match(/\S+/g);
	file = (data[0]+".dat.xml");
	file = file.replace(/\s/g, '');
	proc = data[1];
	
	// document.write("<br>pid = " + data[0]);
	// document.write("<br>file = " + file);
	// document.write("<br>process = " + proc);

</script>

<br/>
<br/>

<?php 
	include 'action_list.php';
?>
<br/>

<div id="pathview">

	<div id="selected_action" >
		<div id="selected_action_name" style="font-weight:bold" ></div>
		<div id="selected_action_state"></div>
		<br/>
		Script:
		<div id="selected_action_script"></div>
	</div>	

	

	<script>
	var x2js = new X2JS();
	var proc_table_loc = file;
	var proc_table;

	var width = 960,
		height = 540;
	
	var NODE_RADIUS = 8;
	var SELECTED_NODE_RADIUS = 12;

	var NODE_SIZE = 200;
	var SELECTED_NODE_SIZE = 400;

	var process_data;
	var prev_click;

	var actions = [];
	var links = [];

	function createActionButtons(d) {
		var button_div = document.createElement("div");
		button_div.setAttribute("id", "action_buttons");
		button_div.innerHTML = "<br />";

	    var button = document.createElement("input");
	    button.type = "button";
	    button.value = "Start Action";
	    button.onclick = function(){
		    alert('Starting action ' + d._name);
		    return false;
		};
		button_div.appendChild(button);

		var button = document.createElement("input");
	    button.type = "button";
	    button.value = "Finish Action";
	    button.onclick = function(){
		    alert('Finishing action ' + d._name);
		    return false;
		};
		button_div.appendChild(button);

	    document.getElementById("selected_action").appendChild(button_div);
	}



	function load_path_data(id) {
		var xmlhttp;
		if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		} else {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.onreadystatechange=function(){
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {

				var xmlData=xmlhttp.responseXML.documentElement;
				
				var x = xmlData.getElementsByTagName("process");

				var proc_data;
				for (var i = 0; i < x.length; i++){
					if (x[i].getAttribute("pid") === id) {
						proc_data = x[i];
					}
				}
				//alert(proc_data.getAttribute("model"));					



				actions = [];
				links = [];
				parseActions(proc_data, actions, links);
				//alert(JSON.stringify(links));

				listActionsInTable(actions);

				if ( actions.length >= 2 ) {
					var length = actions.length;

					// give first node a fixed position
					actions[0].x = 100;
					actions[0].y = height/2;
					actions[0].fixed = true;

					// give last node a fixed position
					actions[length-1].x = width-100;
					actions[length-1].y = height/2;
					actions[length-1].fixed = true;
				}

				force
					.nodes(actions)
					.links(links)
					.start();

				var link = svg.selectAll(".link")
					.data(links)
					.enter().append("line")
					.attr("class", "link")
					.attr("marker-end", "url(#arrow)");

				var node = svg.selectAll("path")
					.data(actions)
					.enter().append("path")
					.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; })
					.attr("d", d3.svg.symbol()
				        .size(function(d) { return NODE_SIZE;})
				        .type(function(d) { if (d.action !=null) {return "circle";} else if (d.decision !=null) {return "diamond";} else {return "square";}}))
					//.enter().append("circle")
					.attr("class", "node")
					//.attr("r", NODE_RADIUS)
				
					.style("fill", function(d) {
						return get_state_colour(d);
					})
					.on("dblclick", function(d) {

						if (d.action != null) {

							// resize last circle clicked on
							if (typeof(prev_click) != "undefined") {
								//d3.select(prev_click).attr("size", NODE_RADIUS);
								d3.select(prev_click).attr("d", d3.svg.symbol().size(NODE_SIZE));
							}

							//d3.select(this).attr("size", SELECTED_NODE_RADIUS);
							d3.select(this).attr("d", d3.svg.symbol().size(SELECTED_NODE_SIZE));

							// activate display box
							document.getElementById("selected_action").style.visibility = "visible";
				
							// display name
							document.getElementById("selected_action_name").innerHTML = d.action._name;
				
							// display state with colour
							document.getElementById("selected_action_state").innerHTML = d.action._state;
							document.getElementById("selected_action_state").style.color = get_state_colour(d);
				
							// display script
							document.getElementById("selected_action_script").innerHTML = d.action.script;

							// clear previous action buttons
							var buttons = document.getElementById("action_buttons");
							if (buttons != null) {
								buttons.parentNode.removeChild(buttons);
							}
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
							if (d.action.req_resource != null) {
								if (d.action.req_resource.length >= 2) { // display list of resources
									for (var i = 0; i < d.action.req_resource.length; i++) {
										var resource = document.createElement("div");
										resource.setAttribute("id", "resource"+i);
										resource.innerHTML = d.action.req_resource[i]._name;
										resources.appendChild(resource);
									}
								} else { // display only one resource
									var resource = document.createElement("div");
									resource.setAttribute("id", "resource");
									resource.innerHTML = d.action.req_resource._name;
									resources.appendChild(resource);								
								}
							}

							// display provided resoures
							var resources = document.createElement("div");
							resources.setAttribute("id", "prov_resources");
							resources.innerHTML = "<br /> Provided Resoures:";
							document.getElementById("selected_action").appendChild(resources);
							if (d.action.prov_resource != null) {
								if (d.action.prov_resource.length >= 2) { // display list of resources
									for (var i = 0; i < d.action.prov_resource.length; i++) {
										var resource = document.createElement("div");
										resource.setAttribute("id", "resource"+i);
										resource.innerHTML = d.action.prov_resource[i]._name;
										resources.appendChild(resource);
									}
								} else { // display only one resource
									var resource = document.createElement("div");
									resource.setAttribute("id", "resource");
									resource.innerHTML = d.action.prov_resource._name;
									resources.appendChild(resource);								
								}
							}

							// display button
							createActionButtons(d.action);

							prev_click = this;
						}
					})
					.call(force.drag);

				node.append("title")
				  .text(function(d) {
				  	if (d.action != null) {return d.action._name;}
				  	else if (d.decision != null) {return "DECISION";}
				  	else if (d.marker != null) {return d.marker.name;}
				  	else {return "";}
				  });

				

				force.on("tick", function() {
					svg.selectAll("path")
      					.attr("transform", function(d) { 
      						if (d.x < 0) d.x = 0;
      						if (d.x > width) d.x = width;
      						if (d.y < 0) d.y = 0;
      						if (d.y > height) d.y = height;
      						return "translate(" + d.x + "," + d.y + ")"; 
      					});

					node.attr("cx", function(d) { return d.x; })
						.attr("cy", function(d) { return d.y; });

					link.attr("x1", function(d) { return d.source.x; })
						.attr("y1", function(d) { return d.source.y; })
						.attr("x2", function(d) { return d.target.x; })
						.attr("y2", function(d) { return d.target.y; });

				});

			}
		}
		xmlhttp.open("GET",proc_table_loc,true);
		xmlhttp.send();
	}

	load_path_data(proc);
	

	var force = d3.layout.force()
		.gravity(0)
		.linkDistance(0.01)
		.linkStrength(1.0)		
		.size([width, height]);

	var svg = d3.select("#pathview").append("svg")
		.attr("width", width)
		.attr("height", height)
		.append("g")
			.call(zm =d3.behavior.zoom().scaleExtent([1,3]).on("zoom", redraw)).on("dblclick.zoom", null);
			//.call(drag);


	var drag = force.drag()
		.origin(function(d) { return d; })
		.on("dragstart", dragstart)
		.on("drag", drag)
		.on("dragend", dragend);


	// draw grey background
	var rect = svg.append("rect")
		.attr("width", width)
		.attr("height", height)
		.attr("fill", "lightgrey")
		.on("click", function() {
			// resize last circle clicked on
			if (typeof(prev_click) != "undefined") {
				//d3.select(prev_click).attr("size", NODE_RADIUS);
				d3.select(prev_click).attr("d", d3.svg.symbol().size(NODE_SIZE));
				
				prev_click = null;
			}
			
			// clear and hide selected action box
			var buttons = document.getElementById("action_buttons");
			if (buttons != null) {
				buttons.parentNode.removeChild(buttons);
			}
			var resources = document.getElementById("resources");
			if (resources != null) {
				resources.parentNode.removeChild(resources);
			}
			document.getElementById("selected_action").style.visibility = "hidden";
		});

	function dragstart(d) {
		d3.event.sourceEvent.stopPropagation();
   		d3.select(this).classed("dragging", true);
		d3.select(this).classed("fixed", d.fixed = true);
	} 

	function drag(d) {
		d3.select(this).attr("cx", d.x = d3.event.x).attr("cy", d.y = d3.event.y);
	}

	function dragend(d) {
		d3.select(this).classed("dragging", false);
	}
	

	function redraw() {
		//node.attr("font-size", (nodeFontSize / d3.event.scale) + "px");
		//svg.selectAll("path").attr("d", d3.svg.symbol().size(function(d) { return NODE_SIZE/d3.event.scale; }));

		// var tx = d3.event.translate[0];
		// var ty = d3.event.translate[1];

		// console.log(svg.node().getBoundingClientRect());

		// var svg_width = svg.node().getBoundingClientRect().width;
		// var svg_height = svg.node().getBoundingClientRect().height;

		// if (tx > 0) tx = 0;
		// if (tx+width < svg_width) tx = svg_width-width;
		// if (ty > 0) ty = 0;
		// if (ty+height < svg_height) ty = svg_height-height;

		// d3.event.translate = [tx, ty];

		svg.attr("transform",
			"translate(" + d3.event.translate + ")"
			+ " scale(" + d3.event.scale + ")");

	}

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
