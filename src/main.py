import sys
from PyQt4 import QtGui

def main():
    
    app = QtGui.QApplication(sys.argv)

    w = QtGui.QWidget()
    w.resize(750, 500)
    w.move(300, 300)
    w.setWindowTitle('KAWAII Patient Records')
    w.show()
    
    sys.exit(app.exec_())


if __name__ == '__main__':
    main()
