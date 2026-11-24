<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\EventListener;

use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Oro\Bundle\SecurityBundle\SecurityFacade;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Pim\Bundle\CustomEntityBundle\EventListener\Action\FormConfigurationListener;
use Pim\Bundle\CustomEntityBundle\EventListener\Action\SecurityListener;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListenersTest extends TestCase
{
    public function testFormListenerDeclaresAndConfiguresConfigurationOptions(): void
    {
        $listener = new FormConfigurationListener();
        $resolver = new OptionsResolver();
        $listener->setConfigurationOptions(new ConfigurationEvent($this->createMock(ConfigurationInterface::class), $resolver));
        $resolver->resolve(['form_type' => 'form']);
        self::assertSame('setConfigurationOptions', FormConfigurationListener::getSubscribedEvents()[ConfigurationEvents::CONFIGURE]);
    }

    public function testSecurityListenerAppliesDefaultsAndRejectsDeniedAcl(): void
    {
        $factory = $this->createMock(ActionFactory::class);
        $security = $this->createMock(SecurityFacade::class);
        $listener = new SecurityListener($factory, $security);
        $action = $this->createMock(ActionInterface::class);
        $action->method('getOptions')->willReturn(['acl' => 'entity_edit']);
        $security->expects(self::once())->method('isGranted')->with('entity_edit')->willReturn(false);
        $this->expectException(AccessDeniedHttpException::class);
        $listener->checkGranted(new ActionEvent($action));
    }
}
