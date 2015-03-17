#!/usr/bin/tclsh8.4

#package require mysqltcl
global mysqlstatus
load mysqltcl-3.051/libmysqltcl3.051.so mysqltcl

#package require mysqltcl

proc isTrue { v } {
  return [expr {![string is false ${v}]}]
}

proc default { path } {
    exists $path
}

proc exists { path } {
    if {[catch {set r $path}]} {
        return 0
    }
    expr [file exists $path]
}

proc ax { path } {
    return 5
}

proc filecount { path } {
    if {[catch {set r $path}]} {
        return 0
    }
    if {![file exists $path]} {
        return 0
    }
    set i 0
    foreach f [exec ls $path] {
        incr {i}
    }
    expr $i
}

proc filesize { path } {
    if {[catch {set r $path}]} {
        return 0
    }
    if {![file exists $path]} {
        return 0
    }
    expr [file size $path]
}

proc timestamp { path } {
    if {[catch {set r $path}]} {
        return 0
    }
    if {![file exists $path]} {
        return 0
    }
    expr [file mtime $path]
}

proc misspellcount { path } {
    if {[catch {set r $path}]} {
        return 0
    }
    if {![file exists $path]} {
        return 0
    }
    set i 0
    foreach f [exec spell $path] {
        incr {i}
    }
    expr $i
}

#returns 0 if no pnotes or patientid is null 1 if pnotes
proc mysqlquery { patientid } {
    set res 0;
    #puts $query
    if { [regexp {^([0-9]+)$} $patientid] } {
        set mysql_handler [mysqlconnect -user "root" -password "password" -db "openemr"]      
        set query {select body from pnotes where pid = }
        lappend query $patientid
        set hl7 [mysqlsel $mysql_handler $query -flatlist] 
        mysqlclose $mysql_handler
        #puts $hl7
        if {$hl7 != {} } {
            set res 1
        }
    }
    #puts $res
    return $res
}
mysqlquery ""