<?php

namespace Pim\Bundle\CustomEntityBundle;

use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ConfigurationBuilderPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\SerializerPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\ManagerRegistryPass;
use Pim\Bundle\CustomEntityBundle\DependencyInjection\Compiler\RegisterFormExtensionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Pim Custom Entity Bundle.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PimCustomEntityBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container
            ->addCompilerPass(new ConfigurationBuilderPass())
            ->addCompilerPass(new SerializerPass())
            ->addCompilerPass(new ManagerRegistryPass());

        if (class_exists('PimEnterprise\Bundle\WorkflowBundle\PimEnterpriseWorkflowBundle')) {
            $container->addCompilerPass(new RegisterFormExtensionPass());
        }
    }
}
