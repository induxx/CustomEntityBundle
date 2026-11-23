<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\TranslatableInterface;
use Acme\Bundle\CustomBundle\Entity\PictogramTranslation;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;

class TranslatableCustomEntity extends AbstractTranslatableCustomEntity
{
    public function getCustomEntityName(): string
    {
        return 'custom_option_name';
    }

    public function getTranslationFQCN()
    {
        return PictogramTranslation::class;
    }
}

class TranslatableCustomEntitySpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CustomEntityBundle\Entity\TranslatableCustomEntity');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(AbstractTranslatableCustomEntity::class);
    }

    public function it_is_translatable(): void
    {
        $this->shouldImplement(TranslatableInterface::class);
    }

    public function it_gets_a_translation(): void
    {
        $this->getTranslationFQCN();
        $this->getTranslation('en_US')->shouldHaveType(PictogramTranslation::class);
    }

    public function it_gets_null_translation_when_no_locale(): void
    {
        $this->getTranslationFQCN();
        $this->getTranslation(null)->shouldReturn(null);
    }
}
