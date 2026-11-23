<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomOptionTranslation;

class CustomOptionTranslation extends AbstractCustomOptionTranslation
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_translation_name';
    }
}

class CustomOptionTranslationSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\CustomOptionTranslation');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractCustomOptionTranslation::class);
    }
}
