<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

ini_set('memory_limit', '1024M');

$kernel = new \App\Kernel('dev', true);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
