<?php

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Duplicates the pim serializer.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 */
class SerializerPass implements CompilerPassInterface
{
    /**
     * @staticvar string
     */
    public const TAG_NAME = 'pim_serializer';

    /**
     * @staticvar integer The default priority for services
     */
    public const DEFAULT_PRIORITY = 100;

    /**
     * @var string
     */
    protected $tagPrefix;

    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds(static::TAG_NAME) as $serviceId => $tags) {
            $serializerTags = [];
            foreach ($tags as $tag) {
                $serializerTags[] = $tag['alias'] ?? $serviceId;
            }

            $this->processService($container, $serviceId, $serializerTags);
        }
    }

    /**
     * process a serializer service.
     *
     * @param string $serviceId
     */
    public function processService(ContainerBuilder $container, $serviceId, array $tags): void
    {
        $tagArguments = [];
        // Looks for all the services tagged "serializer.normalizer" and adds them to the Serializer service
        $tagArguments[0] = $this->findAndSortTaggedServices(
            $container,
            array_map(
                fn ($tag): string => sprintf('%s.normalizer', $tag),
                $tags
            )
        );

        // Looks for all the services tagged "serializer.encoders" and adds them to the Serializer service
        $tagArguments[1] = $this->findAndSortTaggedServices(
            $container,
            array_map(
                fn ($tag): string => sprintf('%s.encoder', $tag),
                $tags
            )
        );

        $definition = $container->getDefinition($serviceId);
        $arguments = $definition->getArguments();
        foreach ($tagArguments as $index => $argument) {
            if (isset($arguments[$index])) {
                $arguments[$index] = array_merge($arguments[$index], $argument);
            } else {
                $arguments[$index] = $argument;
            }
        }

        $definition->setArguments($arguments);
    }

    /**
     * Returns an array of service references for a specified tag name.
     *
     * @param array $tagNames
     *
     * @return Reference[]
     */
    protected function findAndSortTaggedServices(ContainerBuilder $container, $tagNames): mixed
    {
        $sortedServices = [];
        foreach ($tagNames as $tagName) {
            $services = $container->findTaggedServiceIds($tagName);
            foreach ($services as $serviceId => $tags) {
                foreach ($tags as $tag) {
                    $priority = $tag['priority'] ?? self::DEFAULT_PRIORITY;
                    $sortedServices[$priority][] = new Reference($serviceId);
                }
            }
        }

        krsort($sortedServices);

        // Flatten the array
        return array_merge(...$sortedServices);
    }
}
