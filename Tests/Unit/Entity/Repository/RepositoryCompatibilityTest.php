<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Entity\Repository;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomEntityRepository;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\CustomOptionRepository;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomEntityRepository;
use Pim\Bundle\CustomEntityBundle\Entity\Repository\TranslatableCustomOptionRepository;

class RepositoryCompatibilityTest extends TestCase
{
    public function testLegacyRepositoriesAreEmptyAliasesOfTheirCurrentCounterparts(): void
    {
        self::assertSame([], $this->getDeclaredMethods(CustomOptionRepository::class));
        self::assertSame([], $this->getDeclaredMethods(TranslatableCustomOptionRepository::class));
        self::assertTrue(is_subclass_of(CustomOptionRepository::class, CustomEntityRepository::class));
        self::assertTrue(is_subclass_of(TranslatableCustomOptionRepository::class, TranslatableCustomEntityRepository::class));
    }

    private function getDeclaredMethods(string $class): array
    {
        return array_filter((new \ReflectionClass($class))->getMethods(), static function (\ReflectionMethod $method) use ($class): bool {
            return $method->getDeclaringClass()->getName() === $class;
        });
    }

    public function testFindByIdsRejectsEmptyIdsBeforeAccessingPersistence(): void
    {
        $repository = (new \ReflectionClass(CustomEntityRepository::class))->newInstanceWithoutConstructor();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Array must contain at least one reference data id');
        $repository->findByIds([]);
    }
}
