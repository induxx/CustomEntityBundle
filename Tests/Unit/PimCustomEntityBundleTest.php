<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ConfigurationBuilderPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ManagerRegistryPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\SerializerPass;
use Pim\Bundle\CustomEntityBundle\PimCustomEntityBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PimCustomEntityBundleTest extends TestCase
{
    public function testBuildRegistersCoreCompilerPasses(): void
    {
        $container = new ContainerBuilder();
        (new PimCustomEntityBundle())->build($container);

        $passes = $container->getCompilerPassConfig()->getPasses();
        $classes = array_map(get_class(...), $passes);
        self::assertContains(ConfigurationBuilderPass::class, $classes);
        self::assertContains(SerializerPass::class, $classes);
        self::assertContains(ManagerRegistryPass::class, $classes);
    }
}
