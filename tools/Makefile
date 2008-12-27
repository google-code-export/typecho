

#update subversion
svnup:
	@echo 'subverion update'
	svn up ../
	svn export ../ build


package:
	@echo 'package'
	rm -Rf build/mockup/
	rm -Rf build/tools/
    rm -Rf build/todo.txt
	tar -cvvzf build.tar.gz build/


clear:
	rm -Rf build/


all:
	make svnup
	make package
	make clear
