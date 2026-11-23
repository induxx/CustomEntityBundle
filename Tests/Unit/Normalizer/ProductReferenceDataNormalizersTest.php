<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Normalizer;

use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractReferenceData;
use Akeneo\Pim\Enrichment\Component\Product\Repository\ReferenceDataRepositoryResolverInterface;
use Akeneo\Pim\Enrichment\Component\Product\Value\ReferenceDataCollectionValueInterface;
use Akeneo\Pim\Enrichment\Component\Product\Value\ReferenceDataValueInterface;
use Akeneo\Tool\Component\StorageUtils\Repository\IdentifiableObjectRepositoryInterface;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Normalizer\PimDataGridBundle\Product\ReferenceDataCollectionNormalizer;
use Pim\Bundle\CustomEntityBundle\Normalizer\PimDataGridBundle\Product\ReferenceDataNormalizer;

class ProductReferenceDataNormalizersTest extends TestCase
{
    public function testReferenceDataValueNormalizesCodeFallbackAndContext(): void
    {
        $attribute = new class {
            public function getReferenceDataName(): string { return 'colors'; }
        };
        $reference = (new class extends AbstractReferenceData {})->setCode('red');
        $attributeRepository = $this->createMock(IdentifiableObjectRepositoryInterface::class);
        $attributeRepository->method('findOneByIdentifier')->with('color')->willReturn($attribute);
        $repository = $this->createMock(ObjectRepository::class);
        $repository->method('findOneBy')->with(['code' => 'red'])->willReturn($reference);
        $resolver = $this->createMock(ReferenceDataRepositoryResolverInterface::class);
        $resolver->method('resolve')->with('colors')->willReturn($repository);
        $value = $this->createMock(ReferenceDataValueInterface::class);
        $value->method('getAttributeCode')->willReturn('color');
        $value->method('getData')->willReturn('red');
        $value->method('getLocaleCode')->willReturn('en_US');
        $value->method('getScopeCode')->willReturn('ecommerce');
        $normalizer = new ReferenceDataNormalizer($attributeRepository, $resolver);
        self::assertSame(['locale' => 'en_US', 'scope' => 'ecommerce', 'data' => '[red]'], $normalizer->normalize($value, 'datagrid'));
        self::assertTrue($normalizer->supportsNormalization($value, 'datagrid'));
    }

    public function testReferenceDataCollectionNormalizesAndSortsResolvedLabels(): void
    {
        $attribute = new class {
            public function getReferenceDataName(): string { return 'colors'; }
        };
        $red = (new class extends AbstractReferenceData {})->setCode('red');
        $blue = (new class extends AbstractReferenceData {})->setCode('blue');
        $attributeRepository = $this->createMock(IdentifiableObjectRepositoryInterface::class);
        $attributeRepository->method('findOneByIdentifier')->willReturn($attribute);
        $repository = $this->createMock(ObjectRepository::class);
        $repository->method('findOneBy')->willReturnCallback(static fn (array $criteria) => ['red' => $red, 'blue' => $blue][$criteria['code']] ?? null);
        $resolver = $this->createMock(ReferenceDataRepositoryResolverInterface::class);
        $resolver->method('resolve')->willReturn($repository);
        $value = $this->createMock(ReferenceDataCollectionValueInterface::class);
        $value->method('getAttributeCode')->willReturn('color');
        $value->method('getData')->willReturn(['red', 'missing', 'blue']);
        $value->method('getLocaleCode')->willReturn(null);
        $value->method('getScopeCode')->willReturn(null);
        $normalizer = new ReferenceDataCollectionNormalizer($attributeRepository, $resolver);
        self::assertSame(['locale' => null, 'scope' => null, 'data' => '[blue], [red]'], $normalizer->normalize($value, 'datagrid'));
        self::assertTrue($normalizer->supportsNormalization($value, 'datagrid'));
    }
}
