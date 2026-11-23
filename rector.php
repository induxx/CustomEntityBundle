<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
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
        __DIR__ . '/PimCustomEntityBundle.php',
        __DIR__ . '/Tests',
        __DIR__ . '/spec',
    ])
    ->withRules([
        ClassPropertyAssignToConstructorPromotionRector::class,
    ])
    ->withPhpVersion(PhpVersion::PHP_83)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        symfonyCodeQuality: true,
    )
    ->withAttributesSets(symfony: true)
    ->withImportNames(removeUnusedImports: true)
    ->withPhpSets(php83: true);
