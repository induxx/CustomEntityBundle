<?php

namespace Pim\Bundle\CustomEntityBundle\Entity;

use Akeneo\Tool\Component\Localization\Model\AbstractTranslation;
use Akeneo\Tool\Component\Localization\Model\TranslationInterface;

/**
 * @author     Antoine Guigan <antoine@akeneo.com>
 * @copyright  2013 Akeneo SAS (http://www.akeneo.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @deprecated will be removed in 1.7, please use \Akeneo\Tool\Component\Localization\Model\AbstractTranslation
 */
class AbstractCustomOptionTranslation extends AbstractTranslation implements TranslationInterface, \Stringable
{
    /** @var string */
    protected $label;

    /**
     * @param string $label
     */
    public function setLabel($label): AbstractCustomOptionTranslation
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function __toString(): string
    {
        return (string) $this->label;
    }
}
