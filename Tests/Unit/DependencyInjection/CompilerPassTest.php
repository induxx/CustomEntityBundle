<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ConfigurationBuilderPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ManagerRegistryPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\RegisterFormExtensionPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\SerializerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPassTest extends TestCase
{
    public function testManagerRegistryPassRegistersTaggedManagersWithAliases(): void
    {
        $container = new ContainerBuilder();
        $registry = new Definition();
        $container->setDefinition(ManagerRegistryPass::REGISTRY_SERVICE, $registry);
        $container->register('manager.one')->addTag(ManagerRegistryPass::TAG_NAME, ['alias' => 'one']);
        (new ManagerRegistryPass())->process($container);
        $call = $registry->getMethodCalls()[0];
        self::assertSame('add', $call[0]);
        self::assertSame('one', $call[1][0]);
        self::assertSame('manager.one', (string) $call[1][1]);
    }

    public function testSerializerPassInjectsTaggedNormalizers(): void
    {
        $container = new ContainerBuilder();
        $container->register('serializer')->setArguments([[], []])->addTag(SerializerPass::TAG_NAME, ['alias' => 'standard']);
        $container->register('normalizer')->addTag('standard.normalizer');
        (new SerializerPass())->process($container);
        self::assertSame('normalizer', (string) $container->getDefinition('serializer')->getArgument(0)[0]);
    }

    public function testConfigurationBuilderMergesParentDefaults(): void
    {
        $pass = new class extends ConfigurationBuilderPass {
            public function merge(array $child, array $configurations): array
            {
                return $this->getMergedConfiguration($child, $configurations);
            }
        };
        $default = ['class' => 'BaseConfiguration', 'extends' => null, 'options' => [], 'actions' => [], 'entity_class' => 'Entity', 'abstract' => false];
        $parent = ['class' => 'ParentConfiguration', 'extends' => 'default', 'options' => ['parent' => true], 'actions' => ['edit' => ['enabled' => true]], 'entity_class' => 'Entity', 'abstract' => false];
        $child = ['class' => null, 'extends' => 'parent', 'options' => ['child' => true], 'actions' => [], 'entity_class' => 'Entity', 'abstract' => false];
        $merged = $pass->merge($child, ['parent' => $parent, 'default' => $default]);
        self::assertSame('ParentConfiguration', $merged['class']);
        self::assertSame(['child' => true, 'parent' => true], $merged['options']);
        self::assertArrayHasKey('edit', $merged['actions']);
    }

    public function testFormExtensionPassRegistersConfiguredExtensions(): void
    {
        $container = new ContainerBuilder();
        $provider = new Definition();
        $container->setDefinition(RegisterFormExtensionPass::PROVIDER_ID, $provider);
        (new RegisterFormExtensionPass())->process($container);
        $calls = $provider->getMethodCalls();
        self::assertNotEmpty($calls);
        self::assertSame('addExtension', $calls[0][0]);
        self::assertSame('reference-data-job-instance-csv-export-edit-permission', $calls[0][1][0]);
        self::assertNotEmpty($container->getResources());
    }
}
