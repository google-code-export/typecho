

#define root directory
DIR=../


#update subversion
svnup:
	@echo 'subverion update'
	rm -Rf build/
	svn up ../
	svn export ../ build
	for i in `find -name *.source.css`; do java -jar yuicompressor-2.4.2.jar $$i -o $$i; done;
	for i in `find -name *.source.js`; do java -jar yuicompressor-2.4.2.jar $$i -o $$i; done;


package:
	@echo 'package'
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	tar -cvvzf build.tar.gz build/


clear:
	rm -Rf build/


upgrade:
	make svnup
	rm -Rf ${DIR}/admin/
	cp -Rf build/admin/ ${DIR}
	rm -Rf ${DIR}/var/
	cp -Rf build/var/ ${DIR}
	rm -Rf ${DIR}/index.php
	cp build/index.php ${DIR}
	rm -Rf ${DIR}/usr/plugins/CodeEditor/
	rm -Rf ${DIR}/usr/plugins/HelloWorld/
	rm -Rf ${DIR}/usr/plugins/Akismet/
	rm -Rf ${DIR}/usr/plugins/MagikeToTypecho/
	rm -Rf ${DIR}/usr/plugins/WordpressToTypecho/
	rm -Rf ${DIR}/usr/plugins/Textile2/
	rm -Rf ${DIR}/usr/plugins/Helper.php
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
	rm -Rf ${DIR}/usr/upgrade/
	cp -Rf build/usr/plugins/CodeEditor/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/HelloWorld/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Akismet/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/MagikeToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/WordpressToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Textile2/ ${DIR}/usr/plugins/
	cp build/usr/plugins/Helper.php ${DIR}/usr/plugins/
	cp build/usr/plugins/SimpleCode.php ${DIR}/usr/plugins/
	cp -Rf build/usr/upgrade/ ${DIR}/usr/upgrade/
	make clear


install:
	make svnup
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	cp -Rf build/* ${DIR}
	make clear


all:
	make svnup
	make package
	make clear
