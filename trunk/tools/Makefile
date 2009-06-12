

#define root directory
DIR=../
TINYMCE=3_2_2


#update subversion
svnup:
	@echo 'subversion update'
	rm -Rf build/
	svn up ../
	svn export ../ build
	for i in `find -name *.source.css`; do java -Xmx32m -jar yuicompressor-2.4.2.jar $$i -o $$i; done;
	for i in `find build/admin/javascript/ -name *.js`; do java -Xmx32m -jar yuicompressor-2.4.2.jar $$i -o $$i; done;


package:
	@echo 'package'
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	rm -Rf build/usr/plugins/Akismet/
	rm -Rf build/usr/plugins/MagikeToTypecho/
	rm -Rf build/usr/plugins/WordpressToTypecho/
	rm -Rf build/usr/plugins/Textile2/
	rm -Rf build/usr/plugins/PostToQzone/
	rm -Rf build/usr/plugins/SimpleCode.php
	rm -Rf build/usr/plugins/PageToLinks.php
	rm -Rf build/usr/plugins/Creole/
	rm -Rf build/usr/plugins/TinyMCE/
	rm -Rf build/usr/plugins/FlashMp3Player/
	rm -Rf build/usr/plugins/ConnectToTwitter/
	mkdir build/usr/uploads/
	chmod 777 build/usr/uploads/
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
	rm -Rf ${DIR}/usr/plugins/HelloWorld/
	rm -Rf ${DIR}/usr/plugins/Akismet/
	rm -Rf ${DIR}/usr/plugins/MagikeToTypecho/
	rm -Rf ${DIR}/usr/plugins/WordpressToTypecho/
	rm -Rf ${DIR}/usr/plugins/Textile2/
	rm -Rf ${DIR}/usr/plugins/PostToQzone/
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
	rm -Rf ${DIR}/usr/plugins/PageToLinks.php
	rm -Rf ${DIR}/usr/plugins/Creole/
	rm -Rf ${DIR}/usr/plugins/TinyMCE/
	rm -Rf ${DIR}/usr/plugins/FlashMp3Player/
	rm -Rf ${DIR}/usr/plugins/ConnectToTwitter/
	cp -Rf build/usr/plugins/HelloWorld/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Akismet/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/MagikeToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/WordpressToTypecho/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Textile2/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/PostToQzone/ ${DIR}/usr/plugins/
	cp build/usr/plugins/SimpleCode.php ${DIR}/usr/plugins/
	cp build/usr/plugins/PageToLinks.php ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/Creole/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/TinyMCE/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/FlashMp3Player/ ${DIR}/usr/plugins/
	cp -Rf build/usr/plugins/ConnectToTwitter/ ${DIR}/usr/plugins/
	make clear


theme:
	make svnup
	rm -Rf ${DIR}/usr/themes/default/
	cp -Rf build/usr/themes/default/ ${DIR}/usr/themes/
	make clear


typecho:
	for i in `php list.php "build/var/Typecho/"`; do php tc.php "in=$$i&out=build/var/Typecho.php"; done;
	rm -Rf build/var/Typecho/


install:
	make svnup
	rm -Rf build/mockup/
	rm -Rf build/tools/
	rm -Rf build/todo.txt
	rm -Rf ${DIR}/usr/plugins/Akismet/
	rm -Rf ${DIR}/usr/plugins/MagikeToTypecho/
	rm -Rf ${DIR}/usr/plugins/WordpressToTypecho/
	rm -Rf ${DIR}/usr/plugins/Textile2/
	rm -Rf ${DIR}/usr/plugins/PostToQzone/
	rm -Rf ${DIR}/usr/plugins/SimpleCode.php
	rm -Rf ${DIR}/usr/plugins/PageToLinks.php
	rm -Rf ${DIR}/usr/plugins/Creole/
	rm -Rf ${DIR}/usr/plugins/TinyMCE/
	rm -Rf ${DIR}/usr/plugins/FlashMp3Player/
	rm -Rf ${DIR}/usr/plugins/ConnectToTwitter/
	cp -Rf build/* ${DIR}
	make clear


all:
	make svnup
	make package
	make clear
