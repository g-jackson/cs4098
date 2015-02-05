# cs4098
Group Project ~Kawaii~

## Requirements
* Download and Install Qt from: http://www.qt.io/download-open-source/
* Run
  ```
  sudo apt-get install synaptic
  
  sudo apt-get install qt4-dev-tools libqt4-dev libqt4-core libqt4-gui
  
  sudo -s chmod u+x QtSdk-offline-linux-x86_64-v1.2.1.run
  
  sudo -s ./QtSdk-offline-linux-x86_64-v1.2.1.run -style cleanlooks
  
  ```
* Install Qtsdk in /opt
  ```
  sudo -s chmod -R 777 /opt/QtSDK 
  ```
* Insall mysql
  ```
  apt-get install mysql-server-5.6
  ```
* Update
  ```
  sudo apt-get update
  ```
  
## Running the Program on Windows
* Run .pro file

## Running the Program on Linux
* Navigate to the src folder
* ```qmake``` to create the Makefile
* ```make``` to run the Makefile
* ```./cs4098``` to run the program
