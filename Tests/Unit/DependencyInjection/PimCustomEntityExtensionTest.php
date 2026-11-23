<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\PimCustomEntityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PimCustomEntityExtensionTest extends TestCase
{
    public function testLoadsBundleServiceDefinitions(): void
    {
        $container = new ContainerBuilder();
        (new PimCustomEntityExtension())->load([], $container);
        self::assertTrue($container->hasDefinition('pim_custom_entity.action.quick_export'));
        self::assertTrue($container->hasDefinition('pim_custom_entity.manager.registry'));
        self::assertTrue($container->hasDefinition('pim_custom_entity.normalizer.internal_api.custom_entity'));
    }
}
