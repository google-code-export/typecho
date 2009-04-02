<?php
// vim: set et sw=4 ts=4 sts=4 fdm=marker ff=unix fenc=utf8
/**
 * Wiki Parser
 *
 * @author feelinglucky<i.feelinglucky[at]gmail.com>
 *   @link http://www.gracecode.com/
 *   @date 2008-08-20
 */

require_once 'Creole_Wiki.php';
$parser = new Creole_Wiki;
$result['response'] = $parser->transform(trim($wiki));
die(json_encode($result));
?>
