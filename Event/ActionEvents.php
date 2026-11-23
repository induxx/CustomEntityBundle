<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Event;

/**
 * Contains the name of all action events.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionEvents
{
    /**
     * Launched at action configuration.
     */
    public const CONFIGURE = 'pim_custom_entity.action.configure';

    /**
     * Launched before action execution.
     */
    public const PRE_EXECUTE = 'pim_custom_entity.action.pre_execute';

    /**
     * Launched at action configuration.
     */
    public const PRE_RENDER = 'pim_custom_entity.action.pre_render';

    /**
     * Launched after action execution.
     */
    public const POST_EXECUTE = 'pim_custom_entity.action.post_execute';
}
