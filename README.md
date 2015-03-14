# cs4098
##Group Project ~Kawaii~
A project for CS4098 to demonstrate the use of the peos system with pml in a medical record system openEMR

## Project Intallation

### To install the project the following steps must be taken. Further detail on each step is below.
* Downlading this repository 
* Downloading and making the peos kernel from the jnoll/peos repository
* Installing OpenEMR provided in this repository (Version 4.2.0 with modifications in order to add pathway support)

## Requirements for the PEOS Kernel
* Clone the repository at github.com/jnoll/peos
* Run make from the root of the project
* Copy the compiled peos executable from peos/os/kernel into openemr/pathway/
* This compiled executable is the only part of the peos repository that this project requires. 

## Requirements for OpenEMR
To install openEMR on an Ubunutu 14.04 build the following dependancies must be met from the  [Openemr Dependancy Page](http://www.open-emr.org/wiki/index.php/OpenEMR_System_Architecture#OpenEMR_Dependencies):

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

Full Listing of libraries on Ubuntu(14.04):  `sudo apt-get install apache2-mpm-prefork mysql-server libapache2-mod-php5 libdate-calc-perl libdbd-mysql-perl libdbi-perl libhtml-parser-perl libtiff-tools libwww-mechanize-perl libxml-parser-perl php5 php5-mysql php5-cli php5-gd php5-xsl php5-curl php5-mcrypt php-soap imagemagick php5-json `

## OpenEMR Installation

* Copy files from openemr folder to your webserver's root folder
` cp /openemr /var/www/html/ -rf`
* This folder will have to have read/write/execute capabilities by the webserver so chown-ing the openemr folder to the webserver will be nessecary (The required commands will be webserver and platform dependant):
` chown www-data /openemr`
* Browse to http://localhost.com/openemr
* Follow the instructions given 
* For more info go to [Openemr Installation Instructions](http://www.open-emr.org/wiki/index.php/OpenEMR_4.2.0_Linux_Installation)

## Testing Subsystems

### Running the pathway view locally without openemr
* Download and compile the peos kernel from github.com/jnoll/peos
* Copy files from "javascripts" and "path" folders to webserver's root folder
` cp /javascripts /var/www/html/ -rf`
` cp /path /var/www/html/ -rf`
* Provide permissions for the webserver to r/w/x the files in the folders
`chown www-data /openemr/javascripts`
`chown www-data /openemr/path`
* Opening localhost/openemr/pathway/ will display a listing of all the pathways present
* From here you can add and view pathways
