<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Normalizer;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\ExternalApi\MinimalExternalApiNormalizer;
use Symfony\Component\Routing\RouterInterface;

class MinimalExternalApiNormalizerTest extends TestCase
{
    public function testNormalizesCodeAndTranslatedLabels(): void
    {
        $translation = new class {
            public function getLabel(): string { return 'Chaussure'; }
            public function getLocale(): string { return 'fr_FR'; }
        };
        $entity = $this->getMockBuilder(AbstractTranslatableCustomEntity::class)->disableOriginalConstructor()->onlyMethods(['getCode', 'getTranslations'])->getMockForAbstractClass();
        $entity->method('getCode')->willReturn('shoe');
        $entity->method('getTranslations')->willReturn([$translation]);
        $normalizer = new MinimalExternalApiNormalizer($this->createMock(RouterInterface::class));
        self::assertSame(['code' => 'shoe', 'values' => ['label' => [['locale' => 'fr_FR', 'scope' => null, 'data' => 'Chaussure']]]], $normalizer->normalize($entity, 'external_api'));
        self::assertTrue($normalizer->supportsNormalization($entity, 'external_api'));
        self::assertFalse($normalizer->supportsNormalization($entity, 'standard'));
    }
}
