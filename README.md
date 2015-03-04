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

## Compiling the Kernel
To Compile the kernel:

* `make` must be run in `/peos/pml` 

and 

* `make lib` and `make peos` in `/peos/os/kernel`

From root directory:

    cd peos/pml 
    make
    cd ../../..
    cd /cs4098/peos/os/kernel 
    make lib
    make peos


If you get an error related to the file y.tab.h. 

    1. Locate the file y.tab.h in pml/pml folder 
    2. Open it with a text editor that displays the line numbers (like Notepad++)   
    3. At line 39 replace #if with #ifdef 
    4. Save the file 
    5. Retry the above make

## Requirements for OpenEMR
To install openEMR on a fresh Ubuntu 14.04 build the following dependancies must be met from the  [Openemr Dependancy Page](http://www.open-emr.org/wiki/index.php/OpenEMR_System_Architecture#OpenEMR_Dependencies):


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

Full Listing of libraries:  `sudo apt-get install apache2-mpm-prefork mysql-server libapache2-mod-php5 libdate-calc-perl libdbd-mysql-perl libdbi-perl libhtml-parser-perl libtiff-tools libwww-mechanize-perl libxml-parser-perl php5 php5-mysql php5-cli php5-gd php5-xsl php5-curl php5-mcrypt php-soap imagemagick php5-json `

## To Install OpenEMR

* Copy files from openemr folder to your webserver's root folder
` cp /openemr /var/www/html/ -rf`
* **Note**: Permissions may have to be changed to allow modification
* Browse to http://localhost.com/openemr
* Follow the instructions given 
* For more info go to [Openemr Installation Instructions](http://www.open-emr.org/wiki/index.php/OpenEMR_4.2.0_Linux_Installation)

## To run the pathway view locally
* Copy files from "javascripts" and "path" folders to webserver's root folder
` cp /javascripts /var/www/html/ -rf`
` cp /path /var/www/html/ -rf`
* Opening localhost/path/ will display a listing of all the pathways present
* From here you can add and view pathways
 
## Testing peos graphing
Using Firefox is recommended for testing.

* With Firefox, simply opening cs4098/pathview/index.html will work
* For Chrome, you'll need to add the flag --allow-file-access-from-files
* Alternatively you can set up a localhost to run the file from
  - on windows this can be done by installing XAMPP
  - on linux you'll need to add an entry in /etc/hosts and create an Apache vhost configuration
