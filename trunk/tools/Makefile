

#define root directory
DIR=../
TINYMCE=3_2_2


#update subversion
svnup:
	@echo 'subverion update'
	rm -Rf build/
	svn up ../
	svn export ../ build
	for i in `find -name *.source.css`; do java -Xmx32m -jar yuicompressor-2.4.2.jar $$i -o $$i; done;
	for i in `find -name *.source.js`; do java -Xmx32m -jar yuicompressor-2.4.2.jar $$i -o $$i; done;
	for i in `find build/admin/javascript/tiny_mce/ -name *.js`; do java -Xmx32m -jar yuicompressor-2.4.2.jar $$i -o $$i; done;


package:
	@echo 'package'
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	rm -Rf ${DIR}/usr/plugins/CodeEditor/
	rm -Rf ${DIR}/usr/plugins/Akismet/
	rm -Rf ${DIR}/usr/plugins/MagikeToTypecho/
	rm -Rf ${DIR}/usr/plugins/WordpressToTypecho/
	rm -Rf ${DIR}/usr/plugins/Textile2/
	rm -Rf ${DIR}/usr/plugins/PostToQzone/
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
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
	rm -Rf ${DIR}/usr/plugins/PostToQzone/
	rm -Rf ${DIR}/usr/plugins/Helper.php
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
	rm -Rf ${DIR}/usr/upgrade/
	rm -Rf ${DIR}/install/upgrade/
	cp -Rf build/usr/plugins/CodeEditor/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/HelloWorld/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Akismet/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/MagikeToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/WordpressToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Textile2/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/PostToQzone/ ${DIR}/usr/plugins/
	cp build/usr/plugins/SimpleCode.php ${DIR}/usr/plugins/
	cp -Rf build/install/upgrade/ ${DIR}/install/upgrade/
	make clear


tinymce:
	wget http://prdownloads.sourceforge.net/tinymce/tinymce_${TINYMCE}.zip?download
	unzip tinymce_${TINYMCE}.zip
	for i in `find jscripts/ -name *.js`; do php mcecp.php "in=$$i&out=../admin/javascript/tiny_mce/$$i"; done;
	for i in `find jscripts/ -name *.htm`; do php mcecp.php "in=$$i&out=../admin/javascript/tiny_mce/$$i"; done;
	rm -Rf jscripts/
	rm -Rf examples/
	rm -Rf tinymce_${TINYMCE}.zip
	rm -Rf changelog.txt
	svn ci ../admin/javascript/tiny_mce/ -m "upgrade tiny_mce to ${TINYMCE}"


install:
	make svnup
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	rm -Rf ${DIR}/usr/plugins/CodeEditor/
	rm -Rf ${DIR}/usr/plugins/Akismet/
	rm -Rf ${DIR}/usr/plugins/MagikeToTypecho/
	rm -Rf ${DIR}/usr/plugins/WordpressToTypecho/
	rm -Rf ${DIR}/usr/plugins/Textile2/
	rm -Rf ${DIR}/usr/plugins/PostToQzone/
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
	cp -Rf build/* ${DIR}
	make clear


all:
	make svnup
	make package
	make clear
