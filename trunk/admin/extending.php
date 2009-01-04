<?php
include 'common.php';

$panel = Typecho_Request::getParameter('panel');
$panelTable = unserialize($options->panelTable);

if (!isset($panelTable['file']) || !in_array(urlencode($panel), $panelTable['file'])) {
    throw new Typecho_Plugin_Exception(_t('页面不存在'), 404);
}

require_once $panel;
