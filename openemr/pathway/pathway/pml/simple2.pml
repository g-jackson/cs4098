process simple {
    action act_1 {
	    script {"script for action 1"}
		requires {r1}
		provides {r2}
    }
    action act_2 {
	    script {"action 2 script"}
		requires {r2}
		provides {r3}
    }
    action act_3 {
	    script {""}
		requires {r2}
		provides {r4}
    }
    action act_4 {
		requires {r3}
		requires {r4}
		provides {r5}
    }
    action act_5 {
		requires {r5}
		provides {r6}
    }
}
