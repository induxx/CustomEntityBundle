<?php

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Akeneo\Tool\Component\StorageUtils\Remover\RemoverInterface;
use Akeneo\Tool\Component\StorageUtils\Saver\SaverInterface;
use Akeneo\Tool\Component\StorageUtils\Updater\ObjectUpdaterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Base implementation for ORM managers.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Manager implements ManagerInterface
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var ObjectUpdaterInterface */
    protected $updater;

    /** @var SaverInterface */
    protected $saver;

    /** @var RemoverInterface */
    protected $remover;

    public function __construct(
        EntityManagerInterface $em,
        ObjectUpdaterInterface $updater,
        SaverInterface $saver,
        RemoverInterface $remover,
        private readonly NormalizerInterface $normalizer,
    ) {
        $this->em = $em;
        $this->updater = $updater;
        $this->saver = $saver;
        $this->remover = $remover;
    }

    public function create($entityClass, array $defaultValues = [], array $options = []): object
    {
        $referenceData = new $entityClass();
        $this->updater->update($referenceData, $defaultValues);

        return $referenceData;
    }

    public function find($entityClass, $id, array $options = [])
    {
        return $this->em->getRepository($entityClass)->find($id);
    }

    public function findAll($entityClass): array
    {
        return $this->em->getRepository($entityClass)->findAll();
    }

    public function update($entity, array $normalizedData): void
    {
        $this->updater->update($entity, $normalizedData);
    }

    public function save($entity, ?array $options = []): void
    {
        $this->saver->save($entity, $options);
    }

    public function remove($entity): void
    {
        $this->remover->remove($entity);
    }

    public function normalize($entity, $format = null, array $context = []): array
    {
        return $this->normalizer->normalize($entity, $format, $context);
    }
}
