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
