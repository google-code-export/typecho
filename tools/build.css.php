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

$lines = file_get_contents($file);
$result = array();

/** 去掉注释 */
$lines = preg_replace("/\/\*(.*?)\*\//is", '', $lines);
if (preg_match_all("/([\#_a-z0-9-\*\.\:\ \+\>\,]+)\s*\{(.*?)\}/is", $lines, $matches)) {
    foreach ($matches[1] as $key => $val) {
        $items = array_filter(array_map('trim', explode(',', $val)));
        $values = array_filter(array_map('trim', explode(';', $matches[2][$key])));
        
        foreach ($values as $value) {
            list ($name, $property) = array_map('trim', explode(':', $value));
            
            foreach ($items as $item) {
                $result[$name . ': ' . $property][] = $item;
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

file_put_contents($file, $string);
echo 'compiled: ' . $file . "\n";
