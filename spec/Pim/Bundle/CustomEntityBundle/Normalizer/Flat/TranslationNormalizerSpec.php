<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Normalizer\Flat;

use Acme\Bundle\CustomBundle\Entity\PictogramTranslation;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Pim\Bundle\CustomEntityBundle\Normalizer\Flat\TranslationNormalizer;
use Prophecy\Argument;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TranslationNormalizerSpec extends ObjectBehavior
{
    public function let(ClassMetadataRegistry $classMetadataRegistry, PropertyAccessorInterface $propertyAccessor): void
    {
        $this->beConstructedWith($classMetadataRegistry, $propertyAccessor);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TranslationNormalizer::class);
    }

    public function it_is_a_normalizer(): void
    {
        $this->shouldImplement(NormalizerInterface::class);
    }

    public function it_does_not_support_normalization_from_serializer(): void
    {
        $this->supportsNormalization(Argument::any(), Argument::any())->shouldReturn(false);
    }

    public function it_sets_skipped_fields(): void
    {
        $this->setSkippedFields(['id'])->shouldReturn(null);
    }

    public function it_does_not_normalize_skipped_fields(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor,
    ): void {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'id')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'locale')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'foreignKey')->shouldNotBeCalled();
        $propertyAccessor->getValue($translation, 'label')->shouldBeCalled();

        $this->setSkippedFields(['id', 'locale', 'foreignKey'])->shouldReturn(null);
        $this->normalize($translation, 'csv');
    }

    public function it_normalizes_translation(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor,
    ): void {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'label')->willReturn('foo');

        $this->setSkippedFields(['id', 'locale', 'foreignKey'])->shouldReturn(null);

        $this->normalize($translation, 'csv')->shouldReturn(['label-en_US' => 'foo']);
    }

    public function it_normalizes_many_translation_fields(
        PictogramTranslation $translation,
        $classMetadataRegistry,
        $propertyAccessor,
    ): void {
        $translation->getLocale()->willReturn('en_US');

        $classMetadataRegistry
            ->getReadableProperties($translation)
            ->willReturn(['id', 'label', 'locale', 'foreignKey']);

        $propertyAccessor->getValue($translation, 'label')->willReturn('foo');
        $propertyAccessor->getValue($translation, 'locale')->willReturn('bar');

        $this->setSkippedFields(['id', 'foreignKey'])->shouldReturn(null);

        $this->normalize($translation, 'csv')->shouldReturn(['label-en_US' => 'foo', 'locale-en_US' => 'bar']);
    }
}
