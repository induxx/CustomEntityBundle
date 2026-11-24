<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\AbstractTranslation;

/**
 * @author     Antoine Guigan <antoine@akeneo.com>
 * @copyright  2013 Akeneo SAS (http://www.akeneo.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.7,
 *             please use \Pim\Bundle\CustomEntityBundle\Entity\AbstractTranslatableCustomEntity
 */
abstract class AbstractTranslatableCustomOption extends AbstractTranslatableCustomEntity
{
    public function getLabel(): string
    {
        $translated = $this->getTranslation() instanceof AbstractTranslation ? $this->getTranslation()->getLabel() : null;

        return ('' !== $translated && null !== $translated) ? $translated : sprintf('[%s]', $this->getCode());
    }

    public function setLabel(string $label): AbstractTranslatableCustomEntity
    {
        $this->getTranslation()->setLabel($label);

        return $this;
    }

    public static function getSortOrderColumn(): string
    {
        return 'label';
    }

    public static function getLabelProperty(): string
    {
        return 'label';
    }
}
