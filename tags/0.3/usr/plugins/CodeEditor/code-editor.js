/* <![CDATA[ */
/*
Plugin Name: Wordpress Code Editor
Plugin URI: http://www.naden.de/blog/wordpress-code-editor
Description: Javascript für das Wordpress Code Editor Plugin.
Author: Naden Badalgogtapeh
Version: 1.0
Author URI: http://www.naden.de/blog
*/
( function() {
/// hole form und textarea
var ce_form = document.getElementById( 'theme' );
var ce_textarea = document.getElementById( 'content' );
if( ce_form && ce_textarea ) {
	/// hooke ce_form.onsubmit
	ce_form.onsubmit = function() {
		ce_textarea.removeAttribute('disabled');
		ce_form.submit();
	}
	/// dateitype erkennen
	var ce_file = document.getElementsByName( 'edit' );
	var ce_file_type = 'php';
	if( ce_file && ce_file.length > 0 ) {
		if( ce_file[ 0 ].value.match( '\.css' ) ) {
			ce_file_type = 'css';
		}
		else if( ce_file[ 0 ].value.match( '\.js' ) ) {
			ce_file_type = 'js';
		}
		else if( ce_file[ 0 ].value.match( '\.html' ) ) {
			ce_file_type = 'html';
		}
	}
	/// setze Klassenname für codepress
	ce_textarea.className = 'codepress ' + ce_file_type;
	/// lade Editor
	document.write( '<' + 'script type="text/javascript" src="' + window.ce_url + '/codepress/codepress.js"' + '>' + '<' + '/script' + '>' );
}
} )();
/* ]]> */
