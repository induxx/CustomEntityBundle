<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\Standard;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Standard API normalizer, minimal version.
 *
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MinimalStandardNormalizer implements NormalizerInterface
{
    /** @var array */
    protected $supportedFormats = ['standard'];

    /**
     * @param AbstractCustomEntity $entity
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        return [
            'id'   => $entity->getId(),
            'code' => $entity->getCode(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }
}
