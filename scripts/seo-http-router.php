<?php
// Development/CI only; bind PHP server to loopback.
$root = realpath(__DIR__.'/../dist');
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = realpath($root.'/'.rawurldecode($path));
if ($file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file)
    && !in_array($path, ['/index.html','/sitemap.xml'], true)) return false;
$_SERVER['SCRIPT_FILENAME'] = realpath(__DIR__.'/../backend/public/index.php');
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__.'/../backend/public');
require __DIR__.'/../backend/public/index.php';
