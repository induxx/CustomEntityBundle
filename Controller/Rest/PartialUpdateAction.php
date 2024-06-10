<?php

namespace Pim\Bundle\CustomEntityBundle\Controller\Rest;

use Doctrine\ORM\EntityManager;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\ActionInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\Registry as ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PartialUpdateAction extends AbstractRestApiAction implements ActionInterface
{
    protected ValidatorInterface $validator;
    protected NormalizerInterface $violationNormalizer;
    private RouterInterface $router;
    private EntityManager $entityManager;
    private Registry $configurationRegistry;

    public function __construct(
        ActionFactory       $actionFactory,
        ActionEventManager  $eventManager,
        ManagerRegistry     $managerRegistry,
        Registry            $configurationRegistry,
        RouterInterface     $router,
        EntityManager       $entityManager,
        ValidatorInterface  $validator,
        NormalizerInterface $violationNormalizer,
        array               $referenceEntityCodes,
        array               $referenceEntityNames,
    ) {
        parent::__construct($actionFactory, $eventManager, $managerRegistry, $referenceEntityCodes, $referenceEntityNames);
        $this->validator = $validator;
        $this->violationNormalizer = $violationNormalizer;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->configurationRegistry = $configurationRegistry;
    }

    public function __invoke(Request $request, string $referenceCode, string $code): Response
    {
        $referenceEntityClass = $this->getEntityClass($referenceCode);
        if (null === $referenceEntityClass) {
            throw new NotFoundHttpException(sprintf('Reference entity "%s" does not exist.', $referenceCode));
        }

        $this->setConfiguration($this->configurationRegistry->get($this->getConfigurationAlias($referenceCode)));
        $manager = $this->getManager();
        $data = $this->getDecodedContent($request->getContent());
        unset($data['reference-code']);

        $entityRepository = $this->entityManager->getRepository($referenceEntityClass);

        $referenceEntityRecord = $entityRepository->findOneByIdentifier($code);
        $isCreation = null === $referenceEntityRecord;

        if ($isCreation) {
            $referenceEntityRecord = $manager->create($referenceEntityClass, $data);
        } else {
            $manager->update($referenceEntityRecord, $data);
        }

        $errors = $this->validator->validate($referenceEntityRecord);
        if (count($errors) > 0) {
            $normalizedViolations = [];
            foreach ($errors as $error) {
                $normalizedViolations[] = $this->violationNormalizer->normalize(
                    $error,
                    'standard'
                );
            }

            return new JsonResponse(['values' => $normalizedViolations], Response::HTTP_BAD_REQUEST);
        }

        $manager->save($referenceEntityRecord);
        $status = $isCreation ? Response::HTTP_CREATED : Response::HTTP_NO_CONTENT;

        return $this->getResponse($referenceCode, $code, $status);
    }

    private function getResponse(string $referenceCode, string $code, int $status): Response
    {
        $response = new Response(null, $status);
        $route = $this->router->generate(
            'api_reference_entity_get',
            [
                'referenceCode' => $referenceCode,
                'code'    => $code,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $response->headers->set('Location', $route);

        return $response;
    }

    public function getType(): string
    {
        return 'rest_create';
    }
}