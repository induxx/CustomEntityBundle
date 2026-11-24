<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

class CustomEntity extends AbstractCustomEntity
{
    public function getCustomEntityName(): string
    {
        return 'custom_entity_name';
    }
}

class CustomEntitySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomEntity');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractCustomEntity::class);
    }
}
