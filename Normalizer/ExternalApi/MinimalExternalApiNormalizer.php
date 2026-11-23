<?php

namespace Pim\Bundle\CustomEntityBundle\Normalizer\ExternalApi;

use Akeneo\Tool\Component\Api\Hal\Link;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class MinimalExternalApiNormalizer.
 */
class MinimalExternalApiNormalizer implements NormalizerInterface
{
    /** @var array */
    protected $supportedFormats = ['external_api'];

    /**
     * ErpBrandNormalizer constructor.
     */
    public function __construct(private readonly RouterInterface $router)
    {
    }

    /**
     * @param AbstractTranslatableCustomEntity $entity
     */
    public function normalize($entity, $format = null, array $context = []): array
    {
        return [
            'code' => $entity->getCode(),
            'values' => [
                'label' => $this->getLabels($entity),
            ],
        ];
    }

    protected function valueNormalizer($data, ?string $locale = null, ?string $scope = null): array
    {
        return ['locale' => $locale, 'scope' => $scope, 'data' => $data];
    }

    protected function getLabels(AbstractTranslatableCustomEntity $object): array
    {
        $labels = [];
        foreach ($object->getTranslations() as $translation) {
            $labels[] = $this->valueNormalizer($translation->getLabel(), $translation->getLocale());
        }

        return $labels;
    }

    protected function getFileData($fileInfo, string $routing, ?string $locale = null, ?string $scope = null): ?array
    {
        if (!$fileInfo) {
            return [
                'locale' => $locale,
                'channel' => $scope,
                'data' => null,
            ];
        }

        return [
            'locale' => $locale,
            'channel' => $scope,
            'data' => $fileInfo->getKey(),
            '_links' => $this->createLink($fileInfo, $routing),
        ];
    }

    protected function createLink($file, $routing)
    {
        $route = $this->router->generate(
            $routing,
            ['code' => $file->getKey()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $link = new Link('download', $route);

        return $link->toArray();
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof AbstractCustomEntity && in_array($format, $this->supportedFormats);
    }
}
