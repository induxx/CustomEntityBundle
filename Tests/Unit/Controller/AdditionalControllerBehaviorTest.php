<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Controller;

use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry as ConfigurationRegistry;
use Pim\Bundle\CustomEntityBundle\Controller\Rest\PartialUpdateAction;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdditionalControllerBehaviorTest extends TestCase
{
    public function testPartialUpdateRejectsUnknownReferenceType(): void
    {
        $action = new PartialUpdateAction(
            $this->createMock(ActionFactory::class),
            $this->createMock(ActionEventManager::class),
            $this->createMock(Registry::class),
            $this->createMock(ConfigurationRegistry::class),
            $this->createMock(RouterInterface::class),
            $this->createMock(EntityManager::class),
            $this->createMock(ValidatorInterface::class),
            $this->createMock(NormalizerInterface::class),
            [],
            []
        );
        $this->expectException(NotFoundHttpException::class);
        $action(new Request(), 'unknown', 'item');
    }
}
