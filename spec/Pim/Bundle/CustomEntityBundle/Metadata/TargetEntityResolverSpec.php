<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Metadata;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Metadata\TargetEntityResolver;

class TargetEntityResolverSpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em): void
    {
        $this->beConstructedWith($em);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(TargetEntityResolver::class);
    }

    public function it_returns_a_target_entity_class_name_from_its_property($em, ClassMetadata $classMetadata): void
    {
        $object = new FakeEntity();

        $em->getClassMetadata(FakeEntity::class)->willReturn($classMetadata);
        $classMetadata->hasAssociation('foo')->willReturn(true);
        $classMetadata->getAssociationTargetClass('foo')->willReturn('bar');

        $this->getTargetEntityClass($object, 'foo')->shouldReturn('bar');
    }

    public function it_throws_a_logic_exception_if_the_association_has_no_target($em, ClassMetadata $classMetadata): void
    {
        $object = new \stdClass();

        $em->getClassMetadata(\stdClass::class)->willReturn($classMetadata);
        $classMetadata->hasAssociation('foo')->willReturn(false);

        $this->shouldThrow('\LogicException')->during('getTargetEntityClass', [$object, 'foo']);
    }
}
