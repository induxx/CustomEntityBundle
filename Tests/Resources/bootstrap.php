<?php

declare(strict_types=1);

require dirname(__DIR__, 2).'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

$pimDir = dirname(__DIR__, 2).'/vendor/akeneo/pim-community-dev';
(new Dotenv())->usePutenv(true)->bootEnv($pimDir.'/.env', 'test');
$_SERVER += $_ENV;

require $pimDir.'/src/Kernel.php';
require __DIR__.'/TestKernel.php';
