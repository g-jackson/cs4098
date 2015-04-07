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

<body>

<!-- get pid for passing to js -->
<div id="dom-target" style="display: none;">
<?php 
	$pid = $_GET['pid'];
	$procid = $_GET['procid'];
	echo htmlspecialchars($pid . " " . $procid);
?>
</div>

<?php 
$retour .= "<a href='../pathways.php?pid=".$pid."'>Back to Patient Pathway List</a>";
echo $retour;
?>
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

	var NODE_SIZE = 200;
	var SELECTED_NODE_SIZE = 400;

	var process_data;
	var prev_click;

	var actions = [];
	var links = [];

	function get_state_colour(d) {
		if (d.action != null) {
			if (d.action._state == "BLOCKED") {
				return "red";
			} else if (d.action._state == "AVAILABLE") {
				return "orange";
			} else if (d.action._state == "READY") {
				return "yellow";							
			} else if (d.action._state == "COMPLETE") {
				return "green";
			} else {
				return "grey";
			}
		} else if (d.decision != null) {
			return "black";
		}

		return "black";
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

	function addLinks(links, new_links, offset) {
		for (var i = 0; i < new_links.length; i++) {
			var link = {};
			if (new_links[i].source === undefined) {
				alert('found it!');
			}
			link.source = new_links[i].source + offset;
			link.target = new_links[i].target + offset;
			links.push(link);	
		}
	}

	function branch(process_data, actions, links) {
		if (process_data == null) {
			return [];
		}

		var last_selection_nodes = [];
		// add decision node to start of selection
		var branch_start_node;
		if (link_stack.length >= 1) {
			branch_start_node = link_stack.pop();
		} else {
			alert("this shouldn't happen.. I think");
			return;
		}

  		var x = process_data.childNodes;
  		
		for (var i = 0; i < x.length; i++) {
			if (x[i].nodeName === "action") {
				
				var xml_string = new XMLSerializer().serializeToString(x[i]);
				var action = x2js.xml_str2json(xml_string);
				// actions.push(action);
				
				// if (link_stack.length >= 1) {
				// 	var link = {};
				// 	link.source = branch_start_node;					
				// 	link.target = actions.length-1;
				// 	links.push(link);
				// }

				// last_selection_nodes.push(actions.length-1);

			} else if (x[i].nodeName === "sequence") {

				var sequence_nodes = [];
				var sequence_links = [];

				// add link from decision node to current sequence
				link_stack.push(branch_start_node-actions.length);

				parseActions(x[i], sequence_nodes, sequence_links);


				if (sequence_nodes.length >= 1) {
					//alert(JSON.stringify(sequence_nodes));
					addLinks(links, sequence_links, actions.length);
					actions.push.apply(actions, sequence_nodes);

					last_selection_nodes.push(actions.length-1);
				}
			}
		}

		// add node to end of branch
		var node = {};
		node.marker = {};
		node.marker.name = "END_BRANCH_MARKER";
		actions.push(node);
		var selection_end_node = actions.length-1;

		// add links to node at end of branch
		for (var j = 0; j < last_selection_nodes.length; j++) {
			var link = {};
			link.source = last_selection_nodes[j];
			link.target = selection_end_node;
			links.push(link);
		}

		link_stack.push(selection_end_node);
	}

	function selection(process_data, actions, links) {
		if (process_data == null) {
			return [];
		}

		// add decision node to start of selection
		var node = {};
		node.decision = {};
		actions.push(node);
		var decision_node = actions.length-1;
		var last_selection_nodes = [];
		

		// add link to decision node
		if (link_stack.length >= 1) {
			var link = {};
			link.source = link_stack.pop();
			link.target = decision_node;
			links.push(link);
		}

  		var x = process_data.childNodes;
  		
		for (var i = 0; i < x.length; i++) {
			if (x[i].nodeName === "action") {
				
				var xml_string = new XMLSerializer().serializeToString(x[i]);
				var action = x2js.xml_str2json(xml_string);
				// actions.push(action);
				
				// if (link_stack.length >= 1) {
				// 	var link = {};
				// 	link.source = decision_node;					
				// 	link.target = actions.length-1;
				// 	links.push(link);
				// }

				// last_selection_nodes.push(actions.length-1);

			} else if (x[i].nodeName === "sequence") {

				var sequence_nodes = [];
				var sequence_links = [];

				// add link from decision node to current sequence
				link_stack.push(decision_node-actions.length);

				parseActions(x[i], sequence_nodes, sequence_links);


				if (sequence_nodes.length >= 1) {
					//alert(JSON.stringify(sequence_nodes));
					addLinks(links, sequence_links, actions.length);
					actions.push.apply(actions, sequence_nodes);

					last_selection_nodes.push(actions.length-1);
				}
			}
		}

		// add node to end of selection
		var node = {};
		node.marker = {};
		node.marker.name = "END_SELECTION_MARKER";
		actions.push(node);
		var selection_end_node = actions.length-1;

		// add links to node at end of selection
		for (var j = 0; j < last_selection_nodes.length; j++) {
			var link = {};
			link.source = last_selection_nodes[j];
			link.target = selection_end_node;
			links.push(link);
		}

		link_stack.push(selection_end_node);
	}

	var link_stack = [];
	function parseActions(process_data, actions, links) {
		if (process_data == null) {
			return [];
		}

  		var x = process_data.childNodes;
  		
		for (var i = 0; i < x.length; i++) {
			if (x[i].nodeName === "action") {
				
				var xml_string = new XMLSerializer().serializeToString(x[i]);
				var action = x2js.xml_str2json(xml_string);
				actions.push(action);
				
				if (link_stack.length >= 1) {
					var link = {};
					link.source = link_stack.pop();					
					link.target = actions.length-1;
					links.push(link);
				}

				link_stack.push(actions.length-1);

			} else if (x[i].nodeName === "branch") {

				branch(x[i], actions, links);

			} else if (x[i].nodeName === "selection") {

				selection(x[i], actions, links);

			} else if (x[i].nodeName === "iteration") {

				var iteration_start = actions.length;

				// retrieve actions from within iteration
				parseActions(x[i], actions, links);

				// add decision node to end of iteration
				var node = {};
				node.decision = {};
				actions.push(node);

				// add link to decision node from end of iteration
				if (link_stack.length >= 1) {
					var link = {};
					link.source = link_stack.pop();
					link.target = actions.length-1;
					links.push(link);
				}

				// add link back to start of iteration
				var link = {};
				link.source = actions.length-1;
				link.target = iteration_start;
				links.push(link);

				link_stack.push(actions.length-1);

			} else if (x[i].nodeName === "sequence") {

				parseActions(x[i], actions, links);
			}
		}

	}

	var listed_as = "";
	function listActionsByName() {

		var Arr = actions.slice(0);
		if (listed_as === "name_a2z") {
			Arr.sort(function(a, b){ return a.action === undefined ? 1 : b.action === undefined ? -1 : b.action._name.localeCompare(a.action._name); });
			listed_as = "name_z2a";
		} else if (listed_as === "name_z2a" ) {
			Arr.sort(function(a, b){ return a.action === undefined ? -1 : b.action === undefined ? 1 : a.action._name.localeCompare(b.action._name); });
			listed_as = "name_a2z";
		} else { // default
			Arr.sort(function(a, b){ return a.action === undefined ? -1 : b.action === undefined ? 1 : a.action._name.localeCompare(b.action._name); });
			listed_as = "name_a2z";
		}

	    listActionsInTable(Arr);
	};

	function listActionsByState() {
		
		var Arr = actions.slice(0);
		if (listed_as === "state_a2z") {
			Arr.sort(function(a, b){ return a.action === undefined ? 1 : b.action === undefined ? -1 : b.action._state.localeCompare(a.action._state); });
			listed_as = "state_z2a";
		} else if (listed_as === "state_z2a" ) {
			Arr.sort(function(a, b){ return a.action === undefined ? -1 : b.action === undefined ? 1 : a.action._state.localeCompare(b.action._state); });
			listed_as = "state_a2z";
		} else { // default
			Arr.sort(function(a, b){ return a.action === undefined ? -1 : b.action === undefined ? 1 : a.action._state.localeCompare(b.action._state); });
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

		for (var i = 0; i < actions.length; i++) {
			if (actions[i].action != null) {
				tr = document.createElement('tr');
				td = document.createElement('td');
				td.appendChild(document.createTextNode(actions[i].action._name))
				tr.appendChild(td)
				td = document.createElement('td');
				td.appendChild(document.createTextNode(actions[i].action._state))
				td.style.color = get_state_colour(actions[i]);
				tr.appendChild(td)
				tbody.appendChild(tr);
			}
		}

		var old_tbody = document.getElementById("action_list_table_body");
		old_tbody.parentNode.replaceChild(tbody, old_tbody)
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
				//alert(x.length);				

				var proc_data;
				for (i = 0; i < x.length; i++){
					if (x[i].getAttribute("pid") === id) {
						proc_data = x[i];
					}
				}
				//alert(proc_data.getAttribute("model"));					

				var xml_string = new XMLSerializer().serializeToString(xmlhttp.responseXML.documentElement);
				var proc_table = x2js.xml_str2json(xml_string);

				var process_data;
				if (proc_table.process_table.process.length >= 2) {
					process_data = proc_table.process_table.process[id];
				} else {
					process_data = proc_table.process_table.process;
				}

				actions = [];
				links = [];
				parseActions(proc_data, actions, links);
				//alert(JSON.stringify(actions));

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
	
	var zoom = d3.behavior.zoom().on("zoom",function(){
  var t = d3.event.translate;
  var s = d3.event.scale;
  svg.selectAll("rect").attr("transform","translate("+t[0]+","+t[1]+") scale("+s+")")  
}).scaleExtent([1,10]);

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
