<?php

/** 参数不存在则退出 */
if (!isset($argv[1])) {
    echo 'no args';
    exit(1);
}

/** 解析所有的参数 */
parse_str($argv[1], $options);

/** 必要参数检测 */
if (!isset($options['in']) || !isset($options['out'])) {
    echo 'no input or output file';
    exit(1);
}

$in = $options['in'];
$out = str_replace('jscripts/tiny_mce/', '', $options['out']);

if (file_exists($out)) {
	echo $out . "\n";
	unlink($out);
	copy($in, $out);
}

exit(0);
