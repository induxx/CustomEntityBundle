<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Tool\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Manager\Manager;
use Pim\Bundle\CustomEntityBundle\Updater\Updater;
use Prophecy\Argument;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ManagerSpec extends ObjectBehavior
{
    public function let(
        EntityManager $em,
        Updater $updater,
        SaverInterface $saver,
        RemoverInterface $remover,
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($em, $updater, $saver, $remover, $normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Manager::class);
    }

    public function it_creates_objects($updater): void
    {
        $updater->update(Argument::type('stdClass'), ['key1' => 'value1', 'key2' => 'value2'])->shouldBeCalled();
        $this->create('stdClass', ['key1' => 'value1', 'key2' => 'value2'])->shouldHaveType('stdClass');
    }

    public function it_finds_objects($em, EntityRepository $repository): void
    {
        $em->getRepository('entity_class')->willReturn($repository);
        $repository->find('id')->willReturn('success');
        $this->find('entity_class', 'id')->shouldReturn('success');
    }

    public function it_saves_objects($saver): void
    {
        $object = new \stdClass();
        $saver->save($object, [])->shouldBeCalled();
        $this->save($object, []);
    }

    public function it_removes_objects($remover): void
    {
        $object = new \stdClass();
        $remover->remove($object)->shouldBeCalled();
        $this->remove($object);
    }
}
