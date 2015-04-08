import urllib2

urls = ["http://localhost/openemr/interface/super/manage_site_files.php",

"http://localhost/openemr/interface/patient_file/summary/demographics.php",

"http://localhost/openemr/interface/patient_file/carepathway/pathways.php",

"http://localhost/openemr/interface/patient_file/carepathway/pathway/addprocess.php",

"http://localhost/openemr/interface/patient_file/carepathway/pathway/addprocesssubmit.php",
"http://localhost/openemr/interface/patient_file/carepathway/pathway/graph.php"
]

for url in urls:
    req = urllib2.Request(url)
    try:
        resp = urllib2.urlopen(req)
        print url + ": Passed"
    except urllib2.HTTPError as e:
        print e.code
        print url
        exit(0)
print "All good!"

