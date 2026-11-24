<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/Action',
        __DIR__ . '/Configuration',
        __DIR__ . '/Connector',
        __DIR__ . '/Controller',
        __DIR__ . '/DependencyInjection',
        __DIR__ . '/Entity',
        __DIR__ . '/Event',
        __DIR__ . '/EventListener',
        __DIR__ . '/Manager',
        __DIR__ . '/MassAction',
        __DIR__ . '/Metadata',
        __DIR__ . '/Normalizer',
        __DIR__ . '/Remover',
        __DIR__ . '/Resolver',
        __DIR__ . '/Updater',
        __DIR__ . '/Versioning',
        __DIR__ . '/Tests',
        __DIR__ . '/spec',
    ])
    ->name('*.php')
    ->append([__DIR__ . '/PimCustomEntityBundle.php']);

return (new Config())
    ->setRiskyAllowed(false)
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'single_quote' => true,
        'single_line_throw' => false,
    ])
    ->setFinder($finder);
