<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\Standard;

use Akeneo\Tool\Component\FileStorage\Model\FileInfoInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DefaultValuesStandardNormalizer implements NormalizerInterface
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
            'image' => $this->getFileData($entity->getImage()),
            'label' => $entity->getLabel(),
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }

    protected function getFileData(?FileInfoInterface $fileInfo): array
    {
        if (!$fileInfo instanceof FileInfoInterface) {
            return [];
        }

        return [
            'originalFilename' => $fileInfo->getOriginalFilename(),
            'filePath' => $fileInfo->getKey(),
        ];
    }
}
