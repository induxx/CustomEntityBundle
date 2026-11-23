<?php

namespace Pim\Bundle\CustomEntityBundle\Action\Rest;

use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author    JM Leroux <jean-marie.leroux@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractRestAction implements ActionInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * @var ActionEventManager
     */
    protected $eventManager;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $options;

    public function __construct(
        ActionFactory $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry $managerRegistry,
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->managerRegistry = $managerRegistry;
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $this->eventManager->dipatchConfigureEvent($this, $resolver);
        $this->options = $resolver->resolve($configuration->getActionOptions($this->getType()));
    }

    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $optionKey
     *
     * @throws \LogicException
     */
    protected function getOption($optionKey)
    {
        if (isset($this->options[$optionKey])) {
            return $this->options[$optionKey];
        }
        throw new \LogicException(
            sprintf('Option "%s" is not defined', $optionKey)
        );
    }

    public function execute(Request $request): Response
    {
        $this->eventManager->dispatchPreExecuteEvent($this);
        $response = $this->doExecute($request);

        return $this->eventManager->dispatchPostExecuteEvent($this, $response);
    }

    /**
     * Returns the entity of the request.
     *
     * @throws NotFoundHttpException
     */
    protected function findEntity(Request $request): AbstractCustomEntity
    {
        $entity = $this->getManager()->find(
            $this->configuration->getEntityClass(),
            $request->attributes->get('id'),
            $this->options['find_options']
        );

        if (null === $entity) {
            throw new NotFoundHttpException();
        }

        return $entity;
    }

    /**
     * Normalizes an entity into the internal array format.
     */
    protected function normalize(AbstractCustomEntity $entity): array
    {
        $manager = $this->getManager();
        $entityName = $this->configuration->getName();
        $editFormExtension = $this->configuration->getOptions()['edit_form_extension'];
        $context = [
            'customEntityName' => $entityName,
            'form'             => $editFormExtension,
        ];

        return $manager->normalize($entity, 'internal_api', $context);
    }

    /**
     * Returns the custom entity manager.
     */
    protected function getManager(): ManagerInterface
    {
        return $this->managerRegistry->getFromConfiguration($this->configuration);
    }

    /**
     * Set the default options.
     */
    protected function setDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['find_options' => []]);
    }

    /**
     * Executes the action.
     */
    abstract protected function doExecute(Request $request): JsonResponse;
}
