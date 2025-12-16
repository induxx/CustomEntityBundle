<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Channel\Infrastructure\Component\Model\Locale;
use Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface;
use Induxx\Bundle\ReferenceDataBundle\Entity\ReferenceColorParent;
use Webmozart\Assert\Assert;

trait DefaultValuesTrait
{
    private ?FileInfoInterface $image;
    private ?string $label = null;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getImage(): ?FileInfoInterface
    {
        return $this->image;
    }

    public function setImage(?FileInfoInterface $image): void
    {
        $this->image = $image;
    }

    public static function getLabelProperty(): string
    {
        return 'label';
    }
}
