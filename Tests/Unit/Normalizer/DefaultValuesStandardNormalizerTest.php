<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Normalizer;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Normalizer\Standard\DefaultValuesStandardNormalizer;

class DefaultValuesStandardNormalizerTest extends TestCase
{
    public function testSupportsOnlyCustomEntitiesInStandardFormat(): void
    {
        $normalizer = new DefaultValuesStandardNormalizer();
        $entity = $this->createMock(AbstractCustomEntity::class);
        self::assertTrue($normalizer->supportsNormalization($entity, 'standard'));
        self::assertFalse($normalizer->supportsNormalization($entity, 'json'));
        self::assertFalse($normalizer->supportsNormalization(new \stdClass(), 'standard'));
    }

    public function testNormalizesEntityFieldsAndEmptyImage(): void
    {
        $entity = new class extends AbstractCustomEntity {
            public function getImage() { return null; }
            public function getLabel() { return ['en_US' => 'Red']; }
        };
        $entity->setCode('red');
        self::assertSame(['id' => null, 'code' => 'red', 'image' => [], 'label' => ['en_US' => 'Red']], (new DefaultValuesStandardNormalizer())->normalize($entity, 'standard'));
    }
}
