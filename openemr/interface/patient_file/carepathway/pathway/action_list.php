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

</style>


<body>

<div id="action_list" >

Action List:
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

<script>

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

</script>

</body>