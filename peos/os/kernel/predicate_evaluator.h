
#ifndef _PREDICATE_EVALUATOR_H
#define _PREDICATE_EVALUATOR_H
#define USE_INTERP_RESULT
//use on ubuntu
#include </usr/include/tcl/tcl.h>
//use on cygwin
//#include </usr/include/tcl.h>
#ifndef _TCL
#include <tcl/tcl.h>
#endif
#include <pml/tree.h>
#include "action.h"

extern int eval_resource_list(peos_resource_t** resources, int num_resources);
extern int eval_predicate(peos_resource_t* resources, int num_resources, Tree t);

#endif
