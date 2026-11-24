<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Controller;

use Akeneo\Tool\Component\Api\Pagination\PaginatorInterface;
use Akeneo\Tool\Component\Api\Pagination\ParameterValidatorInterface;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Controller\Rest\ReferenceDataController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferenceDataControllerTest extends TestCase
{
    public function testUnknownReferenceTypeIsNotFoundWithoutQueryingDatabase(): void
    {
        $entityManager = $this->createMock(EntityManager::class);
        $entityManager->expects(self::never())->method('getRepository');
        $controller = new ReferenceDataController(
            $this->createMock(NormalizerInterface::class),
            $this->createMock(PaginatorInterface::class),
            $this->createMock(ParameterValidatorInterface::class),
            $entityManager,
            ['pagination' => ['limit_by_default' => 10]],
            []
        );

        $this->expectException(NotFoundHttpException::class);
        $controller->getAction(new Request(), 'unknown', 'item');
    }
}
