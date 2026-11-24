<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\CustomEntityBundle\Controller;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Controller\AjaxOptionController;
use Akeneo\Pim\Structure\Component\ReferenceData\ConfigurationRegistryInterface;
use Doctrine\Persistence\ManagerRegistry;

class AjaxOptionControllerSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $doctrine, ConfigurationRegistryInterface $registry): void
    {
        $this->beConstructedWith($doctrine, $registry);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AjaxOptionController::class);
        $this->shouldBeAnInstanceOf(\Akeneo\Platform\Bundle\UIBundle\Controller\AjaxOptionController::class);
    }
}
