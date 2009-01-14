<?php
/**
 * css编译程序
 * 
 * @author qining
 * @category typecho
 * @package default
 * @copyright Copyright (c) 2008 Typecho team (http://www.typecho.org)
 * @license GNU General Public License 2.0
 * @version $Id$
 */

$file = $argv[1];

if (!is_file($file)) {
    exit;
}

$oldSize = filesize($file);
$lines = file_get_contents($file);
$result = array();
$hacks = array();

function rememberHacks($matches)
{
    global $hacks;
    $hacks[] = trim($matches[1]) . '{' . trim($matches[2]) . '{' . trim($matches[3]) . '}}';
    return NULL;
}

$lines = preg_replace_callback("/([\#_a-z0-9-\*\.\:\ \+\>\,\(\)\@]+)\s*\{\s*([\#_a-z0-9-\*\.\:\ \+\>\,]+)\s*\{(.*?)\}\s*\}/is", 'rememberHacks', $lines);


/** 去掉注释 */
$lines = preg_replace("/\/\*(.*?)\*\//is", '', $lines);
if (preg_match_all("/([\#_a-z0-9-\*\.\:\ \+\>\,]+)\s*\{(.*?)\}/is", $lines, $matches)) {
    foreach ($matches[1] as $key => $val) {
        $items = array_filter(array_map('trim', explode(',', $val)));
        $values = array_filter(array_map('trim', explode(';', $matches[2][$key])));
        
        foreach ($values as $value) {
            list ($name, $property) = array_map('trim', explode(':', $value));
            
            foreach ($items as $item) {
                $result[$name . ':' . $property][] = $item;
            }
        }
    }
}

$final = array();
foreach ($result as $key => $val) {
    $final[implode(',', $val)][] = $key;
}

$string = '';

foreach ($final as $key => $val) {
    $string .= $key . '{' . implode(';', $val) . '}';
}

$string .= implode('', $hacks);
file_put_contents($file, $string);
$newSize = filesize($file);
echo 'compiled: ' . $file . " {$oldSize} -> {$newSize}\n";
