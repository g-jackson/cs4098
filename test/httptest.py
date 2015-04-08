import urllib2

urls = ["http://localhost/openemr"]

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

