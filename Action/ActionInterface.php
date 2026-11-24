<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Action;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Common interface for actions.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ActionInterface
{
    /**
     * Sets the configuration.
     */
    public function setConfiguration(ConfigurationInterface $configuration);

    /**
     * Returns the configuration.
     */
    public function getConfiguration(): ConfigurationInterface;

    /**
     * Execute the action.
     */
    public function execute(Request $request): Response;

    public function getType(): string;

    public function getOptions(): array;
}
