<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvents;
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
        $listener->setConfigurationOptions(new ConfigurationEvent($this->createMock(\Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface::class), $resolver));
        $resolver->resolve(['form_type' => 'form']);
        self::assertSame('setConfigurationOptions', FormConfigurationListener::getSubscribedEvents()[ConfigurationEvents::CONFIGURE]);
    }

    public function testSecurityListenerAppliesDefaultsAndRejectsDeniedAcl(): void
    {
        $factory = $this->createMock(\Pim\Bundle\CustomEntityBundle\Action\ActionFactory::class);
        $security = $this->createMock(\Oro\Bundle\SecurityBundle\SecurityFacade::class);
        $listener = new SecurityListener($factory, $security);
        $action = $this->createMock(ActionInterface::class);
        $action->method('getOptions')->willReturn(['acl' => 'entity_edit']);
        $security->expects(self::once())->method('isGranted')->with('entity_edit')->willReturn(false);
        $this->expectException(\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException::class);
        $listener->checkGranted(new ActionEvent($action));
    }
}
