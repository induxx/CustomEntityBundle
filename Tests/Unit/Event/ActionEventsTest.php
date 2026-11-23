<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Event\ActionEvents;
use Pim\Bundle\CustomEntityBundle\Event\ConfigureActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvent;
use Pim\Bundle\CustomEntityBundle\Event\ConfigurationEvents;
use Pim\Bundle\CustomEntityBundle\Event\PreRenderActionEvent;
use Pim\Bundle\CustomEntityBundle\Event\PostExecuteActionEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionEventsTest extends TestCase
{
    public function testActionEventRetainsAction(): void
    {
        $action = $this->createMock(ActionInterface::class);
        self::assertSame($action, (new ActionEvent($action))->getAction());
    }

    public function testSubeventsExposeAndAllowMutationOfValues(): void
    {
        $action = $this->createMock(ActionInterface::class);
        $resolver = new OptionsResolver();
        $configure = new ConfigureActionEvent($action, $resolver);
        self::assertSame($resolver, $configure->getOptionsResolver());
        $pre = new PreRenderActionEvent($action, 'before.html', ['a' => 1]);
        $pre->setTemplate('after.html');
        $pre->setTemplateVars(['b' => 2]);
        self::assertSame(['after.html', ['b' => 2]], [$pre->getTemplate(), $pre->getTemplateVars()]);
        $response = new Response('before');
        $post = new PostExecuteActionEvent($action, $response);
        $replacement = new Response('after');
        $post->setResponse($replacement);
        self::assertSame($replacement, $post->getResponse());
    }

    public function testManagerDispatchesAndReturnsListenerMutations(): void
    {
        $action = $this->createMock(ActionInterface::class);
        $dispatcher = new EventDispatcher();
        $manager = new ActionEventManager($dispatcher);
        $response = new Response('new');
        $dispatcher->addListener(ActionEvents::POST_EXECUTE, static function (PostExecuteActionEvent $event) use ($response): void { $event->setResponse($response); });
        self::assertSame($response, $manager->dispatchPostExecuteEvent($action, new Response('old')));
        $dispatcher->addListener(ActionEvents::PRE_RENDER, static function (PreRenderActionEvent $event): void { $event->setTemplate('changed'); });
        self::assertSame(['changed', ['x' => 1]], $manager->dispatchPreRenderEvent($action, 'original', ['x' => 1]));
    }

    public function testConfigurationEventExposesCollaborators(): void
    {
        $configuration = $this->createMock(\Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface::class);
        $resolver = new OptionsResolver();
        $event = new ConfigurationEvent($configuration, $resolver);
        self::assertSame($configuration, $event->getConfiguration());
        self::assertSame($resolver, $event->getOptionsResolver());
        self::assertIsString(ConfigurationEvents::CONFIGURE);
    }
}
