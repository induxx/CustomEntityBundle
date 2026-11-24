<?php

declare(strict_types=1);

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\TranslatableInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

class TranslatableCustomOption extends AbstractTranslatableCustomEntity
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_name';
    }

    public function getTranslationFQCN()
    {
        return 'translation_fqcn';
    }
}

class TranslatableCustomOptionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf(TranslatableCustomOption::class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractCustomEntity::class);
    }

    public function it_is_translatable(): void
    {
        $this->shouldImplement(TranslatableInterface::class);
    }
}
