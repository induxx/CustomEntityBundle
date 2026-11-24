<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Versioning;

use Akeneo\Tool\Bundle\VersioningBundle\UpdateGuesser\UpdateGuesserInterface;
use Doctrine\ORM\EntityManager;

/**
 * Custom entity update guesser.
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class UpdateGuesser implements UpdateGuesserInterface
{
    public function supportAction($action)
    {
        return UpdateGuesserInterface::ACTION_UPDATE_ENTITY == $action;
    }

    public function guessUpdates(EntityManager $em, $entity, $action)
    {
        $pendings = [];
        if ($entity instanceof VersionableInterface) {
            $pendings[] = $entity;
        }

        return $pendings;
    }
}
