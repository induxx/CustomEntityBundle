<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Versioning;

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Versioning\UpdateGuesser;
use Pim\Bundle\CustomEntityBundle\Versioning\VersionableInterface;
use Akeneo\Tool\Bundle\VersioningBundle\UpdateGuesser\UpdateGuesserInterface;

class UpdateGuesserSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(UpdateGuesser::class);
    }

    public function it_is_an_update_guesser(): void
    {
        $this->shouldImplement(UpdateGuesserInterface::class);
    }

    public function it_only_supports_update_actions(): void
    {
        $this->supportAction(UpdateGuesserInterface::ACTION_UPDATE_ENTITY)->shouldReturn(true);
        $this->supportAction(UpdateGuesserInterface::ACTION_DELETE)->shouldReturn(false);
        $this->supportAction(UpdateGuesserInterface::ACTION_UPDATE_COLLECTION)->shouldReturn(false);
        $this->supportAction('any_other_action')->shouldReturn(false);
    }

    public function it_guesses_updates_for_versionable_entities(EntityManager $em, VersionableInterface $entity): void
    {
        $this->guessUpdates($em, $entity, UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
             ->shouldReturn([$entity]);

        $this->guessUpdates($em, new \stdClass(), UpdateGuesserInterface::ACTION_UPDATE_ENTITY)
             ->shouldReturn([]);
    }
}
