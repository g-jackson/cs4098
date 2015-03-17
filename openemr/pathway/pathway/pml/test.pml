process sample5 {
  action act_0 {
  	requires{t}
    script {"test script"}
    provides{t}
  }
  action act_1 {
    script {"test script"}
    requires{t}
    provides{y}
  }
 }

