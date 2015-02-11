# cs4098
Group Project ~Kawaii~

## Requirements for the Kernel
To install program on a fresh Ubuntu 14.04 build the following dependancies must be met:

Libraries:
install git
install byacc
install bison
install tcl-dev
install flex
install check
install libreadline-dev
install libncurses5-dev
install libxml2
install libxml2-dev
install openssl

Full Listing of libraries:
sudo apt-get install git byacc bison tcl-dev flex check libreadline-dev libncurses5-dev libxml2 libxml2-dev openssl

## Running the kernel
git clone https://github.com/g-jackson/cs4098
cd cs4098/peos/pml 
make
cd ../../..
cd /cs4098/peos/os/kernel 
make lib
make peos

If you get an error related to the file y.tab.h. 
	Locate the file y.tab.h in pml/pml folder 
	Open it with a text editor that displays the line numbers (like Notepad++) 	
	At line 39 replace #if with #ifdef 
	Save the file 
	Retry the above make

====================================================================================================================

To install program on Windows using Cygwin:

Add the following libraries in the cygwin setup.exe:
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
pthread

Download project from https://github.com/g-jackson/cs4098

cd cs4098/peos/pml 
make
cd ../../..
cd /cs4098/peos/os/kernel 
make lib
make peos

If you should get an error related to the file y.tab.h. 
	Locate the file y.tab.h in pml/pml folder 
	Open it with a text editor that displays the line numbers (like Notepad++) 	
	At line 39 replace #if with #ifdef 
	Save the file 
	Retry the above make

## To run the pathway view locally
With Firefox, simply opening pathview/index.html will work
For Chrome, you'll need to add the flag --allow-file-access-from-files
	or alternatively you can set up a localhost to run the file from
		on windows this can be done by installing XAMPP
		on linux you'll need to add an entry in /etc/hosts and create an Apache vhost configuration
	I really recommend just using Chrome
