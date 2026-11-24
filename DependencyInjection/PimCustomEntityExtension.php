<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PimCustomEntityExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $loader->load('actions.yml');
        $loader->load('connectors.yml');
        $loader->load('controllers.yml');
        $loader->load('event_listeners.yml');
        $loader->load('jobs.yml');
        $loader->load('job_parameters.yml');
        $loader->load('managers.yml');
        $loader->load('mass_actions.yml');
        $loader->load('metadata.yml');
        $loader->load('savers.yml');
        $loader->load('serializer.yml');
        $loader->load('services.yml');
        $loader->load('update_guessers.yml');
    }
}
