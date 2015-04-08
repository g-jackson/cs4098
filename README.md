# cs4098
## Group Project ~Kawaii~
A project for CS4098 to demonstrate the use of the peos system with pml in a medical record system openEMR

### Project Installation

To install the project the following steps must be taken. Further detail on each step is below.
* Downlading this repository 
* Downloading and making the peos kernel from the jnoll/peos repository
* Installing OpenEMR available at http://www.open-emr.org/ (Version 4.2.0 with modifications in order to add pathway support)
* Overwriting OpenEMR files with the openemr folder provided in our repository

### Requirements for the PEOS Kernel
* Clone the repository at http://github.com/jnoll/peos
* Install its dependencies
* Run make from the root of the project
* Copy the compiled peos executable from peos/os/kernel into openemr/interface/patient_file/carepathway/pathway
* This compiled executable is the only part of the peos repository that this project requires. 

### Requirements for OpenEMR
To install openEMR on an Ubunutu 14.04 build the following dependencies must be met from the  [Openemr Dependency Page](http://www.open-emr.org/wiki/index.php/OpenEMR_System_Architecture#OpenEMR_Dependencies):

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

### OpenEMR and Pathways Installation

* Download and Run OpenEMR .deb installer from here: http://sourceforge.net/projects/openemr/files/OpenEMR%20Ubuntu_debian%20Package/4.1.2.7/openemr_4.1.2-3_all.deb/download
* This installs 'openemr' folder into webroot
* This folder will have to have read/write/execute capabilities by the webserver so chown-ing the openemr folder to the webserver will be nessecary (The required commands will be webserver and platform dependant):
* `sudo chown -R www-data openemr`
* Browse to http://localhost.com/openemr
* Follow the instructions given 
* For more info go to [Openemr Installation Instructions](http://www.open-emr.org/wiki/index.php/OpenEMR_4.2.0_Linux_Installation)
 
### Features
Below we will describe each feature on our backlog and how to use it.
##### Real EMR interface
Our pathway system integrates directly into OpenEMR which is a real EMR interface. We describe how to install and access OpenEMR in the OpenEMR Installation section above.
##### XML parsing
The peos generates a xml listing of the current processes that are running, representing pathways. We parse this XML and display it both as a graph and table interface. To view this functionality login to openemr and go to a patient's file page or create a new one if one does not exist already. There will be a link to the pathway view for that patient which, when clicked, takes you to a table displaying the pathways that the patient has currently. This table view is parsed from the xml in a patients current processes and can be updated by adding new processes. The graph view links from this table also parse the peos's xml file to obtain the graphs' data.
##### Task list
From the patient pathway listing, select a pathway to view. This will take you to a page that displays all of a pathways actions in a neat table. Clicking on the head of a table column will sort the the table by that field. This allows the user to easily find an action by it's name or quickly find available actions.
##### Basic pathway graph view
This is displayed on the same page as the task list. The graph displays pathway actions as circles and decisions (found in selections and iterations) as diamonds. The graph also contains squares. These mark the end of branches and selections. Links are generated based on keywords and action ordering in the xml. This display the flow of activity throught the graph with arrows. The first action can be found at the leftmost point on the graph and the last action at the rightmost. All nodes int eh graph can be repositioned by clicking and draging. The graph also displays the state of actions by colouring nodes by state.
##### Pan and zoom
To zoom on the graph, hover your mouse over the graph and use the mouse scroll-wheel or on a laptop trackpad pinch or splay your fingertips. Click and drag the light-grey background of the graph to pan.
##### Kernel interface
We use php to make calls to the peos. Our interface allows new pathways to be added and viewed on a per patient basis. To view this functionality login to openemr and go to a patient's file page or create a new one if one does not exist already. There will be a link to the pathway view for that patient which, when clicked, takes you to a table displaying the pathways that the patient has currently. The option to add more pathways from here is an interface with the kernel instructing it to add a new process to the peos.
##### Resource access
We currently do not support this feature.
##### Refined graph view
More information can be displayed within the graph by double clicking on an action node. This will cause a display box to appear in the corner of the graph with useful information such as script, required resources and provided resources. Buttons have also been added to display the future potential of a pathways interface. To hide the display box click on the grey background of the graph.

### Automated Tests
#### HTTP Tests
Dependencies

    python2.7

To run:
* Navigate to the root folder of the repository
* Run: `python test/httptest.py`

#### Graph Tests
For the graph, there are two sets of tests.
* The first set of tests ensures that graph data is being parsed from xml and is being stored with the correct nodes and links for the graph to display.
* The second set of tests checks that the actions listed in the table above the graph are displayed correctly and that on calling the sort table function the information displayed in the table is correctly sorted.

To run: 
* Navigate to http://localhost/openemr/interface/patient_file/carepathway/pathway/test/graph_test.php 
