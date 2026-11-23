<?php

namespace Pim\Bundle\CustomEntityBundle\Event;

use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Event manager for actions.
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ActionEventManager
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Constructor.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Dispatches the ActionEvents::CONFIGURE event.
     */
    public function dipatchConfigureEvent(ActionInterface $action, OptionsResolver $optionsResolver): void
    {
        $event = new ConfigureActionEvent($action, $optionsResolver);
        $this->eventDispatcher->dispatch($event, ActionEvents::CONFIGURE);
    }

    /**
     * Dispatches the ActionEvents::PRE_EXECUTE event.
     */
    public function dispatchPreExecuteEvent(ActionInterface $action): void
    {
        $event = new ActionEvent($action);
        $this->eventDispatcher->dispatch($event, ActionEvents::PRE_EXECUTE);
    }

    /**
     * Dispatches the ActionEvents::POST_EXECUTE event, and returns the modified response.
     */
    public function dispatchPostExecuteEvent(ActionInterface $action, Response $response): Response
    {
        $event = new PostExecuteActionEvent($action, $response);
        $this->eventDispatcher->dispatch($event, ActionEvents::POST_EXECUTE);

        return $event->getResponse();
    }

    /**
     * Dispatches the ActionEvents::PRE_RENDER event, and returns an array containing the template ant its parameters.
     *
     * @param string $template
     */
    public function dispatchPreRenderEvent(ActionInterface $action, $template, array $templateVars): array
    {
        $event = new PreRenderActionEvent($action, $template, $templateVars);
        $this->eventDispatcher->dispatch($event, ActionEvents::PRE_RENDER);

        return [$event->getTemplate(), $event->getTemplateVars()];
    }
}
