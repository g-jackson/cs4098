<!DOCTYPE html>
<meta charset="utf-8">
<style>

table {
    width:100%;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#action_list_table tr:nth-child(even) {
    background-color: #eee;
}
table#action_list_table tr:nth-child(odd) {
   background-color:#fff;
}
table#action_list_table th	{
    background-color: #FFAC84;
    color: black;
}

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
</head>

<script src="../javascripts/jquery.min.js"></script> 
    <script> 
    $(function(){
      $("#navbar").load("navbar.html"); 
    });
    </script> 
<div id="navbar"></div>
<br>

<body>


<!-- get pid for passing to js -->
<div id="dom-target" style="display: none;">
<?php 
	$pid = $_GET['pid'];
	$procid = $_GET['procid'];
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
	document.write("<br>file = " + file);
	document.write("<br>process = " + proc);
</script>

<br/>
<br/>
<div id="action_list" >
Actions:

<table id="action_list_table">
<thead>
	<tr>
		<th onclick="listActionsByName()">Name</th>
		<th onclick="listActionsByState()">State</th>		
	</tr>
</thead>
<tbody id="action_list_table_body">
	<tr>
		<td>test name</td>
		<td>test state</td>		
	</tr>
</tbody>
</table>

</div>
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

	var process_data;
	var prev_click;

	var links = [];

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

	var actions = [];
	function parseActions(process_data) {
		if (process_data == null) {
			return;
		}

		parseActions(process_data.iteration);

		if (process_data.action == null) { // no actions
			// nothing to link
		} else if (process_data.action.length >= 2) { // list of actions
			for (var i = 0; i < process_data.action.length; i++) {
				actions.push(process_data.action[i]);
			}
		} else { // only one action
			actions.push(process_data.action);
		}

	}

	var listed_as = "";
	function listActionsByName() {

		var Arr = actions.slice(0);
		if (listed_as === "name_a2z") {
			Arr.sort(function(a, b){return b._name.localeCompare(a._name)});
			listed_as = "name_z2a";
		} else if (listed_as === "name_z2a" ) {
			Arr.sort(function(a, b){return a._name.localeCompare(b._name)});
			listed_as = "name_a2z";
		} else { // default
			Arr.sort(function(a, b){return a._name.localeCompare(b._name)});	
			listed_as = "name_a2z";
		}

	    listActionsInTable(Arr);
	};

	function listActionsByState() {
		
		var Arr = actions.slice(0);
		if (listed_as === "state_a2z") {
			Arr.sort(function(a, b){return b._state.localeCompare(a._state)});
			listed_as = "state_z2a";
		} else if (listed_as === "state_z2a" ) {
			Arr.sort(function(a, b){return a._state.localeCompare(b._state)});
			listed_as = "state_a2z";
		} else { // default
			Arr.sort(function(a, b){return a._state.localeCompare(b._state)});
			listed_as = "state_a2z";
		}

		listActionsInTable(Arr);	    
	};

	function listActionsInTable(actions) {
		if (actions == null) {
			return;
		}

		var tbody = document.createElement('tbody');
		tbody.setAttribute("id", "action_list_table_body");

		var tr, td;
		if (actions.length >= 2) { // list of actions

			for (var i = 0; i < actions.length; i++) {
				tr = document.createElement('tr');
				td = document.createElement('td');
				td.appendChild(document.createTextNode(actions[i]._name))
				tr.appendChild(td)
				td = document.createElement('td');
				td.appendChild(document.createTextNode(actions[i]._state))
				td.style.color = get_state_colour(actions[i]);
				tr.appendChild(td)
				tbody.appendChild(tr);
			}

		} else { // only one action

			tr = document.createElement('tr');
			td = document.createElement('td');
			td.appendChild(document.createTextNode(actions._name))
			tr.appendChild(td)
			td = document.createElement('td');
			td.appendChild(document.createTextNode(actions._state))
			td.style.color = get_state_colour(actions);
			tr.appendChild(td)
			tbody.appendChild(tr);
		}

		var old_tbody = document.getElementById("action_list_table_body");
		old_tbody.parentNode.replaceChild(tbody, old_tbody)
	}

	function creatLinkForReqResource(process_data, req_resource_name, req_resource_action_index) {
		

		// for each action
		for (var act_j = 0; act_j < process_data.action.length; act_j++) {

			// search through required provided resources
			if (process_data.action[act_j].prov_resource == null) { // no provided resources
				// nothing to link
			} else if (process_data.action[act_j].prov_resource.length >= 2) { // list of resources

				// and link them to actions providing that resource
				for (var res_i = 0; res_i < process_data.action[act_j].prov_resource.length; res_i++) {
					var resource_name = process_data.action[act_j].prov_resource[res_i]._name;
					if (req_resource_name === resource_name) {
						var link = {};
						link.source = act_j;
						link.target = req_resource_action_index;
						links.push(link);
					}
				}

			} else { // only one resource
				var resource_name = process_data.action[act_j].prov_resource._name;
				if (req_resource_name === resource_name) {
					var link = {};
					link.source = act_j;
					link.target = req_resource_action_index;
					links.push(link);
				}
			}
			
		}
	}

	function generateLinks(process_data) {
		// nothing to link if only one action or less
		if (process_data.action === null) {
			return;
		} 

		if (process_data.action.length >= 2) {
			
			// for each action
			for (var act_i = 0; act_i < process_data.action.length; act_i++) {

				// search through required required resources
				if (process_data.action[act_i].req_resource == null) { // no required resources
					// nothing to link
				} else if (process_data.action[act_i].req_resource.length >= 2) { // list of resources

					// and link them to actions providing that resource
					for (var res_i = 0; res_i < process_data.action[act_i].req_resource.length; res_i++) {
						var resource_name = process_data.action[act_i].req_resource[res_i]._name;
						creatLinkForReqResource(process_data, resource_name, act_i);
					}

				} else { // only one resource
					var resource_name = process_data.action[act_i].req_resource._name;
					creatLinkForReqResource(process_data, resource_name, act_i);
				}
				
			}

		}
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

				var xml_string = new XMLSerializer().serializeToString(xmlhttp.responseXML.documentElement);
				var proc_table = x2js.xml_str2json(xml_string);

				var process_data;
				if (proc_table.process_table.process.length >= 2) {
					process_data = proc_table.process_table.process[id];
				} else {
					process_data = proc_table.process_table.process;
				}

				//alert(JSON.stringify(process_data[0]));
				parseActions(process_data);
				//alert(JSON.stringify(actions));
				
				generateLinks(process_data);
				// alert(JSON.stringify(links));

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
					.gravity(0)
					.charge(15)
					.linkStrength(0.1)
					.chargeDistance(5)
					.nodes(actions)
					.links(links)
					.start();

				var link = svg.selectAll(".link")
					.data(links)
					.enter().append("line")
					.attr("class", "link")
					.attr("marker-end", "url(#arrow)");

				var node = svg.selectAll(".node")
					.data(actions)
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
						document.getElementById("selected_action_state").innerHTML = d._state;
						document.getElementById("selected_action_state").style.color = get_state_colour(d);
			
						// display script
						document.getElementById("selected_action_script").innerHTML = d.script;

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

						// display button
						createActionButtons(d);

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
		
		.size([width, height]);

	var svg = d3.select("#pathview").append("svg")
		.attr("width", width)
		.attr("height", height);

	var drag = force.drag()
		.on("dragstart", dragstart); 

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
		d3.select(this).classed("fixed", d.fixed = true);
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
