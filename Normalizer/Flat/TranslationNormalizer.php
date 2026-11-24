<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\Flat;

use Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface;
use Pim\Bundle\CustomEntityBundle\Metadata\ClassMetadataRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a translation interface in a flat array.
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2018 Akeneo SAS (http://www.akeneo.com)
 */
class TranslationNormalizer implements NormalizerInterface
{
    /** @var ClassMetadataRegistry */
    protected $classMetadataRegistry;

    /** @var PropertyAccessorInterface */
    protected $propertyAccessor;

    /** @var string[] */
    protected $skippedFields = [];

    public function __construct(
        ClassMetadataRegistry $classMetadataRegistry,
        PropertyAccessorInterface $propertyAccessor,
    ) {
        $this->classMetadataRegistry = $classMetadataRegistry;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function normalize($translation, $format = null, array $context = [])
    {
        $normalizedData = [];
        $transProperties = $this->classMetadataRegistry->getReadableProperties($translation);
        $transProperties = array_diff($transProperties, $this->skippedFields);

        foreach ($transProperties as $transProperty) {
            $transValue = $this->propertyAccessor->getValue($translation, $transProperty);
            if (!is_object($transValue) && !is_array($transValue)) {
                $normalizedData[sprintf('%s-%s', $transProperty, $translation->getLocale())] = $transValue;

                continue;
            }

            if ($transValue instanceof FileInfoInterface) {
                $normalizedData[sprintf('%s-%s', $transProperty, $translation->getLocale())] = $transValue->getKey();
            }
        }

        return $normalizedData;
    }

    /**
     * {@inheritdoc}
     *
     * We do NOT want to call it from Serializer
     */
    public function supportsNormalization($data, $format = null)
    {
        return false;
    }

    public function setSkippedFields(array $skippedFields): void
    {
        $this->skippedFields = $skippedFields;
    }
}
