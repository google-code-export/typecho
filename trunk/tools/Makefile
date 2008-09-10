#update subversion
svnup:
	@echo 'subverion update'
	svn up ../
	svn export ../ build
	rm -Rf build/mockup/
	rm -Rf build/tools/
	tar -cvvzf build.tar.gz build/
	rm -Rf build/
