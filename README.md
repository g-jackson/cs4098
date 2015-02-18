# cs4098
##Group Project ~Kawaii~
A project for CS4098 to demonstrate the use of the peos system with pml in a medical record system openEMR

## Requirements for the Kernel
To install program on a fresh Ubuntu 14.04 build the following dependancies must be met:

Libraries:
    git
    byacc
    bison
    tcl-dev
    flex
    check
    libreadline-dev
    libncurses5-dev
    libxml2
    libxml2-dev
    openssl


Full Listing of libraries:
`sudo apt-get install git byacc bison tcl-dev flex check libreadline-dev libncurses5-dev libxml2 libxml2-dev openssl`

## Running the kernel
```
git clone https://github.com/g-jackson/cs4098
cd cs4098/peos/pml 
make
cd ../../..
cd /cs4098/peos/os/kernel 
make lib
make peos
```

If you get an error related to the file y.tab.h. 
	1. Locate the file y.tab.h in pml/pml folder 
	2. Open it with a text editor that displays the line numbers (like Notepad++) 	
	3. At line 39 replace #if with #ifdef 
	4. Save the file 
	5. Retry the above make

## Requirements for OpenEMR
To install openEMR on a fresh Ubuntu 14.04 build the following dependancies must be met (from the openEMR Installation page (http://www.open-emr.org/wiki/index.php/OpenEMR_System_Architecture#OpenEMR_Dependencies):


Libraries:
    apache2-mpm-prefork
    mysql-server
    libapache2-mod-php5
    libdate-calc-perl
    libdbd-mysql-perl
    libdbi-perl
    libhtml-parser-perl
    libtiff-tools
    libwww-mechanize-perl
    libxml-parser-perl
    php5
    php5-mysql
    php5-cli
    php5-gd
    php5-xsl
    php5-curl
    php5-mcrypt
    php-soap
    imagemagick 
    php5-json 


`sudo apt-get install apache2-mpm-prefork mysql-server libapache2-mod-php5 libdate-calc-perl libdbd-mysql-perl libdbi-perl libhtml-parser-perl libtiff-tools libwww-mechanize-perl libxml-parser-perl php5 php5-mysql php5-cli php5-gd php5-xsl php5-curl php5-mcrypt php-soap imagemagick php5-json `

## To Install OpenEMR
	*Create a symlink from the /openemr folder to the root of the webserver
    ` ln -sd /openemr /var/www/html/ `
    Note: Permissions may have to be changed to allow modification

	*Browse to http://localhost.com/openemr

	*Follow the instructions given
	For more info go to (http://www.open-emr.org/wiki/index.php/OpenEMR_4.2.0_Linux_Installation)

## To run the pathway view locally
With Firefox, simply opening pathview/index.html will work
For Chrome, you'll need to add the flag --allow-file-access-from-files
	or alternatively you can set up a localhost to run the file from
		on windows this can be done by installing XAMPP
		on linux you'll need to add an entry in /etc/hosts and create an Apache vhost configuration
	I really recommend just using Chrome
