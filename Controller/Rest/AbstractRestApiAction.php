<?php

namespace Pim\Bundle\CustomEntityBundle\Controller\Rest;

use Andres\Bundle\ReferenceDataBundle\Entity\ColourSpecific;
use Andres\Bundle\ReferenceDataBundle\Entity\FabricProperties;
use Andres\Bundle\ReferenceDataBundle\Entity\Mannequin;
use Andres\Bundle\ReferenceDataBundle\Entity\WashingInstructions;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractRestApiAction
{
    protected ActionFactory $actionFactory;

    protected ActionEventManager $eventManager;

    protected ManagerRegistry $managerRegistry;

    protected ConfigurationInterface $configuration;

    protected array $options;

    private array $referenceEntityCodes;
    private array $referenceEntityNames;

    public function __construct(
        ActionFactory      $actionFactory,
        ActionEventManager $eventManager,
        ManagerRegistry    $managerRegistry,
        array              $referenceEntityCodes,
        array              $referenceEntityNames,
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->managerRegistry = $managerRegistry;
        $this->referenceEntityCodes = $referenceEntityCodes;
        $this->referenceEntityNames = $referenceEntityNames;
    }

    public function setConfiguration(ConfigurationInterface $configuration): void
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

    protected function getOption($optionKey)
    {
        if (isset($this->options[$optionKey])) {
            return $this->options[$optionKey];
        } else {
            throw new \LogicException(
                sprintf('Option "%s" is not defined', $optionKey)
            );
        }
    }

    public function execute(Request $request): Response
    {
        throw new \Exception('WE DON\'T USE EXECUTE, WE BYPASS IT');
    }

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

    protected function normalize(AbstractCustomEntity $entity): array
    {
        $manager = $this->getManager();
        $entityName = $this->configuration->getName();
        $editFormExtension = $this->configuration->getOptions()['edit_form_extension'];
        $context = [
            'customEntityName' => $entityName,
            'form'             => $editFormExtension,
        ];

        return $manager->normalize($entity, 'standard', $context);
    }

    protected function getDecodedContent($content): array
    {
        $decodedContent = \json_decode($content, true);

        if (null === $decodedContent) {
            throw new BadRequestHttpException('Invalid json message received');
        }

        return $decodedContent;
    }

    protected function getManager(): ManagerInterface
    {
        return $this->managerRegistry->getFromConfiguration($this->configuration);
    }

    protected function setDefaultOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['find_options' => []]);
    }

    /**
     * @param string $referenceEntityCode
     * @return string
     */
    protected function getEntityClass(string $referenceEntityCode): ?string
    {
        return $this->referenceEntityCodes[$referenceEntityCode] ?? null;
    }

    /**
     * TODO extract these params outside the class
     */
    protected function getConfigurationAlias(string $referenceEntityCode): ?string
    {
        return $this->referenceEntityNames[$referenceEntityCode] ?? null;
    }
}