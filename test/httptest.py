import urllib2

url = "http://localhost/notthere"
req = urllib2.Request(url)
try:
    resp = urllib2.urlopen(req)
except urllib2.HTTPError as e:
    print e.code
    print url
    exit(0)
print "All good!"

