<?php

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

umask(0000);

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('test', false);
$kernel->loadClassCache();
$kernel = new AppCache($kernel);
$kernel->handle(Request::createFromGlobals())->send();