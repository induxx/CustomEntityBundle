<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOption;

class CustomOption extends AbstractCustomOption
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_name';
    }
}

class CustomOptionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomOption');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractCustomOption::class);
    }
}
