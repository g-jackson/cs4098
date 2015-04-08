<!DOCTYPE html>
<meta charset="utf-8">

<head>
	<script src="../../javascripts/d3.min.js"></script>
	<script src="../../javascripts/xml2json.min.js"></script>

	<script src="../xml_action_parsing.js"></script>
</head>

<body>

<?php 
	include '../action_list.php';
	// echo "action_list.php loaded";
?>

<div id="tests">
</div>

<script>

	var x2js = new X2JS();
	var test_data_loc = "graph_test_data.xml";

	var table = document.getElementById("action_list");
	table.style.visibility='hidden';

	var static_actions_list = [];
	var static_links_list = [];

	var simple_graph_actions = [{"action":{"script":"(null)","req_resource":{"_name":"r1","_value":"\"\\${r1}\"","_qualifier":""},"prov_resource":{"_name":"r2","_value":"\"\\${r2}\"","_qualifier":""},"_name":"act_1","_state":"BLOCKED"}},{"action":{"script":"(null)","req_resource":{"_name":"r2","_value":"\"\\${r2}\"","_qualifier":""},"prov_resource":{"_name":"r2","_value":"\"\\${r2}\"","_qualifier":""},"_name":"act_2","_state":"NONE"}}];
	var simpler_graph_actions = [{"action":{"script":"(null)","req_resource":{"_name":"r1","_value":"\"\\${r1}\"","_qualifier":""},"prov_resource":{"_name":"r2","_value":"\"\\${r2}\"","_qualifier":""},"_name":"act_1","_state":"BLOCKED"}}];
	var proc_test_graph_actions = [{"action":{"script":"\"Login to test host as $test_user.\"","req_resource":{"_name":"test_user","_value":"\"\\${test_user}\"","_qualifier":""},"_name":"login_as_testuser","_state":"BLOCKED"}},{"action":{"script":"\"Run `cvs release $working_dir; rm -r $working_dir.\"","req_resource":{"_name":"working_dir","_value":"\"\\${working_dir}\"","_qualifier":""},"_name":"delete_old_workspace","_state":"NONE"}},{"action":{"script":"\"Run `cvs checkout $test_module'.\"","prov_resource":{"_name":"working_dir","_value":"\"\\${working_dir}\"","_qualifier":""},"_name":"checkout_workspace","_state":"AVAILABLE"}},{"action":{"script":"\"Run `make test' in `$working_dir/src' directory.\"","req_resource":{"_name":"working_dir","_value":"\"\\${working_dir}\"","_qualifier":""},"_name":"run_tests","_state":"NONE"}},{"decision":{}},{"action":{"script":"\"If all tests passed, you are finished; add this to your\nlist of accomplishments for today. If not, go back and fix any\nfailures uncovered.\"","req_resource":{"_name":"working_dir","_value":"\"\\${working_dir}\"","_qualifier":""},"_name":"update_status_report","_state":"BLOCKED"}},{"action":{"script":"\"You are finished. Get a cup of coffee!\"","req_resource":{"_name":"working_dir","_value":"\"\\${working_dir}\"","_qualifier":""},"_name":"complete_commit","_state":"NONE"}}];
	var run_peos_graph_actions = [{"action":{"script":"\"cvs -d $repository checkout peos-test\"","req_resource":{"_name":"repository","_value":"\"\\${repository}\"","_qualifier":""},"prov_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"checkout","_state":"BLOCKED"}},{"action":{"script":"\"Check if '/home/jntestuser/tcl_install/include/tcl.h' exists.  If not change machine.\"","_name":"check_tcltk","_state":"AVAILABLE"}},{"action":{"script":"\"run 'cd $workspace/src/os/kernel'\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"goto_kernel_dir","_state":"NONE"}},{"action":{"script":"\"run make\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"build_kernel","_state":"NONE"}},{"action":{"script":"\"fix any failures\"","_name":"fix_kernel_failures","_state":"AVAILABLE"}},{"decision":{}},{"decision":{}},{"action":{"script":"\"run 'cd $workspace/src/ui/GUI'\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"goto_gtk_dir","_state":"NONE"}},{"action":{"script":"\"run make\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"build_gtk_app","_state":"NONE"}},{"action":{"script":"\"fix any failures\"","_name":"fix_gtk_failures","_state":"AVAILABLE"}},{"decision":{}},{"action":{"script":"\"run ./gtkpeos\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"run_gtk_app","_state":"NONE"}},{"action":{"script":"\"run 'cd $workspace/src/ui/java-gui'\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"goto_gtk_dir","_state":"NONE"}},{"action":{"script":"\"\n                        run . /etc/profile\n                        run setup jdk-1.4.0\n                        run export CLASSPATH=/home/jnoll/lib/xmlParserAPIs.jar:/home/jnoll/lib/junit/junit.jar:/home/jnoll/lib/xercesImpl.jar\"","_name":"set_java_env","_state":"AVAILABLE"}},{"action":{"script":"\"run make\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"build_java_app","_state":"NONE"}},{"action":{"script":"\"fix any failures\"","_name":"fix_java_failures","_state":"AVAILABLE"}},{"decision":{}},{"action":{"script":"\"run ./runpeos\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"run_java_app","_state":"NONE"}},{"action":{"script":"\"run 'cd $workspace/src/ui/web2'\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"goto_web_dir","_state":"NONE"}},{"action":{"script":"\"run export HTML_DIR=$html_dir\"","req_resource":{"_name":"html_dir","_value":"\"\\${html_dir}\"","_qualifier":""},"_name":"set_html_dir","_state":"NONE"}},{"action":{"script":"\"run make install\"","req_resource":{"_name":"workspace","_value":"\"\\${workspace}\"","_qualifier":""},"_name":"build_web_app","_state":"NONE"}},{"action":{"script":"\"fix any failures\"","_name":"fix_web_failures","_state":"AVAILABLE"}},{"decision":{}},{"action":{"script":"\"\n                        run a web browser\n                        goto $peos_url\"","req_resource":{"_name":"peos_url","_value":"\"\\${peos_url}\"","_qualifier":""},"_name":"run_web_app","_state":"NONE"}},{"marker":{"name":"END_SELECTION_MARKER"}}];
	static_actions_list[0] = proc_test_graph_actions;
	static_actions_list[1] = simple_graph_actions;
	static_actions_list[2] = simpler_graph_actions;
	static_actions_list[3] = run_peos_graph_actions;

	var proc_test_links = [{"source":0,"target":1},{"source":1,"target":2},{"source":2,"target":3},{"source":3,"target":4},{"source":4,"target":0},{"source":4,"target":5},{"source":5,"target":6}];
	var simple_links = [{"source":0,"target":1}];
	var simpler_links = [];
	var run_peos_links = [{"source":0,"target":1},{"source":1,"target":2},{"source":2,"target":3},{"source":3,"target":4},{"source":4,"target":5},{"source":5,"target":3},{"source":5,"target":6},{"source":6,"target":7},{"source":7,"target":8},{"source":8,"target":9},{"source":9,"target":10},{"source":10,"target":8},{"source":10,"target":11},{"source":6,"target":12},{"source":12,"target":13},{"source":13,"target":14},{"source":14,"target":15},{"source":15,"target":16},{"source":16,"target":14},{"source":16,"target":17},{"source":6,"target":18},{"source":18,"target":19},{"source":19,"target":20},{"source":20,"target":21},{"source":21,"target":22},{"source":22,"target":20},{"source":22,"target":23},{"source":11,"target":24},{"source":17,"target":24},{"source":23,"target":24}];
	static_links_list[0] = proc_test_links;
	static_links_list[1] = simple_links;
	static_links_list[2] = simpler_links;
	static_links_list[3] = run_peos_links;

	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange=function(){
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {

			var xmlData=xmlhttp.responseXML.documentElement;
				
				var test_results = document.createElement("div");
				test_results.setAttribute("id", "test_results");
				var results_str = "";

				results_str += ("<H4>Testing XMLparsing and generation of graph data</H4>");
				var tests = 0;
				var tests_passed = 0;
				
				var x = xmlData.getElementsByTagName("process");
				var proc_data;
				for (i = 0; i < x.length; i++){
						proc_data = x[i];
						results_str += ("loaded " + proc_data.getAttribute('model'));
						results_str += ("<br/>");

						actions = [];
						links = [];
						link_stack = [];
						parseActions(proc_data, actions, links);

						// test actions
						results_str += ("comparing static action list to generated action list...<br/>");
						var static_actions = static_actions_list[i];
						var graph1 = JSON.stringify(actions);
						var graph2 = JSON.stringify(static_actions);
						if (graph1 === graph2) {
							results_str += ("test passed<br/>");
							tests_passed++;
						} else {
							results_str += ("test failed<br/>");
						}
						tests++;

						// test links
						results_str += ("comparing static links to generated links...<br/>");
						var static_links = static_links_list[i];
						var links1 = JSON.stringify(links);
						var links2 = JSON.stringify(static_links);
						if (links1 === links2) {
							results_str += ("test passed<br/>");
							tests_passed++;
						} else {
							results_str += ("test failed<br/>");
						}
						results_str += ("<br/>");
						tests++;
				}

				results_str += (tests_passed + "/" + tests + " tests passed");

				test_results.innerHTML = results_str;
    			var tests = document.getElementById("tests");
    			tests.appendChild(test_results);
		}
	}

	document.write("<H4>Testing Action List</H4>");
	var tests = 0;
var tests_passed = 0;

	// first test
	listActionsInTable(simple_graph_actions);
	document.write("comparing table generated for simple.pml with expected results<br/>");
	var expected_results = [
		["Name", "State"],
		["act_1", "BLOCKED"],
		["act_2", "NONE"],
	];
	var failed = false;

	var table = document.getElementById('action_list_table');
    for (var r = 0, n = table.rows.length; r < n; r++) {
        for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
        	if (table.rows[r].cells[c].innerHTML != expected_results[r][c]) {
        		failed = true;
        	}
        }
    }
    tests++;
    if (failed === true) {
    	document.write("test failed<br/>");
    } else {
    	document.write("test passed<br/>");
    	tests_passed++;
    }
	document.write("<br/>");

	// second test
	listActionsInTable(proc_test_graph_actions);
	document.write("comparing table generated for proc_test.pml with expected results<br/>");
	var expected_results = [
		["Name","State"],
		["login_as_testuser","BLOCKED"],
		["delete_old_workspace","NONE"],
		["checkout_workspace","AVAILABLE"],
		["run_tests","NONE"],
		["update_status_report","BLOCKED"],
		["complete_commit","NONE"]
	];
	var failed = false;

	var table = document.getElementById('action_list_table');
    for (var r = 0, n = table.rows.length; r < n; r++) {
        for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
        	if (table.rows[r].cells[c].innerHTML != expected_results[r][c]) {
        		failed = true;
        	}
        }
    }
    tests++;
    if (failed === true) {
    	document.write("test failed<br/>");
    } else {
    	document.write("test passed<br/>");
    	tests_passed++;
    }
    document.write("<br/>");

    // third test
    actions = proc_test_graph_actions;
	listActionsByName();
	document.write("sorting table by name and comparing with expected results<br/>");
	var expected_results = [
		["Name","State"],
		["checkout_workspace","AVAILABLE"],
		["complete_commit","NONE"],
		["delete_old_workspace","NONE"],
		["login_as_testuser","BLOCKED"],
		["run_tests","NONE"],
		["update_status_report","BLOCKED"]
		
	];
	var failed = false;

	var table = document.getElementById('action_list_table');
    for (var r = 0, n = table.rows.length; r < n; r++) {
        for (var c = 0, m = table.rows[r].cells.length; c < m; c++) {
        	if (table.rows[r].cells[c].innerHTML != expected_results[r][c]) {
        		failed = true;
        	}
        }
    }
    tests++;
    if (failed === true) {
    	document.write("test failed<br/>");
    } else {
    	document.write("test passed<br/>");
    	tests_passed++;
    }
    document.write("<br/>");

    document.write(tests_passed + "/" + tests + " tests passed");

	xmlhttp.open("GET", test_data_loc, true);
	xmlhttp.send();


</script>