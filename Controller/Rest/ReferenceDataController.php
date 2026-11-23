<?php

namespace Pim\Bundle\CustomEntityBundle\Controller\Rest;

use Akeneo\Tool\Component\Api\Exception\PaginationParametersException;
use Akeneo\Tool\Component\Api\Pagination\PaginatorInterface;
use Akeneo\Tool\Component\Api\Pagination\ParameterValidatorInterface;
use Akeneo\Tool\Component\Api\Repository\ApiResourceRepositoryInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * mimicks the API interfaces and responses of reference-entities.
 */
class ReferenceDataController
{
    /** @var ApiResourceRepositoryInterface */
    private $repository;

    public function __construct(private readonly NormalizerInterface $normalizer, private readonly PaginatorInterface $paginator, private readonly ParameterValidatorInterface $parameterValidator, private readonly EntityManager $entityManager, private array $apiConfiguration, private array $referenceEntityCodes)
    {
    }

    public function getAction(Request $request, string $referenceCode, string $code): JsonResponse
    {
        $referenceEntityClass = $this->getEntityClass($referenceCode);
        if (null === $referenceEntityClass) {
            throw new NotFoundHttpException(sprintf('Reference entity "%s" does not exist.', $referenceCode));
        }

        $entityRepository = $this->entityManager->getRepository($referenceEntityClass);

        $referenceEntityRecord = $entityRepository->findOneByIdentifier($code);
        if (null === $referenceEntityRecord) {
            throw new NotFoundHttpException(sprintf('Reference entity record "%s" does not exist.', $code));
        }

        $normalisedReferenceEntity = $this->normalizer->normalize($referenceEntityRecord, 'external_api');

        return new JsonResponse($normalisedReferenceEntity);
    }

    public function listAction(Request $request, string $referenceCode): JsonResponse
    {
        $referenceEntityClass = $this->getEntityClass($referenceCode);
        if (null === $referenceEntityClass) {
            throw new NotFoundHttpException(sprintf('Reference entity "%s" does not exist.', $referenceCode));
        }

        $entityRepository = $this->entityManager->getRepository($referenceEntityClass);

        try {
            $this->parameterValidator->validate($request->query->all());
        } catch (PaginationParametersException $paginationParametersException) {
            throw new UnprocessableEntityHttpException($paginationParametersException->getMessage(), $paginationParametersException);
        }

        $defaultParameters = [
            'page'       => 1,
            'limit'      => $this->apiConfiguration['pagination']['limit_by_default'],
            'with_count' => 'false',
        ];

        $queryParameters = array_merge($defaultParameters, $request->query->all());
        $offset = $queryParameters['limit'] * ($queryParameters['page'] - 1);
        $referenceEntity = $entityRepository->searchAfterOffset(
            [],
            ['code' => 'ASC'],
            $queryParameters['limit'],
            $offset
        );

        $parameters = [
            'uri_parameters'      => ['referenceCode' => $referenceCode],
            'query_parameters'    => $queryParameters,
            'list_route_name'     => 'api_reference_entity_list',
            'item_route_name'     => 'api_reference_entity_get',
        ];

        $count = true === $request->query->getBoolean('with_count') ? $this->repository->count() : null;
        $paginatedReferenceEntity = $this->paginator->paginate(
            $this->normalizer->normalize($referenceEntity, 'external_api'),
            $parameters,
            $count
        );

        return new JsonResponse($paginatedReferenceEntity);
    }

    /**
     * Get the JSON decoded content. If the content is not a valid JSON, it throws an error 400.
     *
     * @param string $content content of a request to decode
     *
     * @throws BadRequestHttpException
     */
    protected function getDecodedContent($content): array
    {
        $decodedContent = json_decode($content, true);

        if (null === $decodedContent) {
            throw new BadRequestHttpException('Invalid json message received');
        }

        return $decodedContent;
    }

    private function getEntityClass(string $referenceEntityCode): ?string
    {
        return $this->referenceEntityCodes[$referenceEntityCode] ?? null;
    }
}
