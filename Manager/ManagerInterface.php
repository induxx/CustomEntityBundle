<?php

declare(strict_types=1);

namespace Pim\Bundle\CustomEntityBundle\Manager;

use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;

/**
 * Base interface for custom entity managers.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
interface ManagerInterface
{
    /**
     * Creates an entity.
     *
     * @param string $entityClass
     *
     * @return object
     */
    public function create($entityClass, array $defaultValues = [], array $options = []);

    /**
     * Find an entity by id, returns null if the object is not found.
     *
     * @param string $entityClass
     *
     * @return AbstractCustomEntity|null
     */
    public function find($entityClass, $id, array $options = []);

    /**
     * Find all entities, returns empty array if none are found.
     *
     * @param string $entityClass
     */
    public function findAll($entityClass): array;

    /**
     * Saves the entity.
     *
     * @param object $entity
     */
    public function update($entity, array $normalizedData): void;

    /**
     * Saves the entity.
     *
     * @param object $entity
     */
    public function save($entity, array $options = []): void;

    /**
     * Removes the entity.
     *
     * @param object $entity
     */
    public function remove($entity): void;

    /**
     * Normalizes custom entity object to array.
     */
    public function normalize($entity, $format = null, array $context = []): array;
}
