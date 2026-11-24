<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Pim\Enrichment\Component\Product\Model\AbstractReferenceData;

/**
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractCustomEntity extends AbstractReferenceData
{
    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setCreated(\DateTime $created): AbstractCustomEntity
    {
        $this->created = $created;

        return $this;
    }

    public function setUpdated(\DateTime $updated): AbstractCustomEntity
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Returns the custom entity name used in the configuration
     * Used to map row actions on datagrid.
     */
    public function getCustomEntityName(): string
    {
        return strtolower(implode('', array_slice(explode('\\', static::class), -1)));
    }

    public static function getLabelProperty(): string
    {
        return 'code';
    }

    /**
     * Returns the sort order.
     */
    public static function getSortOrderColumn(): string
    {
        return 'code';
    }
}
