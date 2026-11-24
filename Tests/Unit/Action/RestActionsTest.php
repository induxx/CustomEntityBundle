<?php

namespace Pim\Bundle\CustomEntityBundle\Tests\Unit\Action;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use PHPUnit\Framework\TestCase;
use Pim\Bundle\CustomEntityBundle\Action\ActionFactory;
use Pim\Bundle\CustomEntityBundle\Action\QuickExportAction;
use Akeneo\Tool\Bundle\BatchBundle\Job\JobInstanceRepository;
use Akeneo\Tool\Bundle\BatchBundle\Launcher\JobLauncherInterface;
use Akeneo\Tool\Component\Batch\Model\JobInstance;
use Oro\Bundle\DataGridBundle\Extension\MassAction\MassActionParametersParser;
use Oro\Bundle\PimDataGridBundle\Adapter\GridFilterAdapterInterface;
use Oro\Bundle\PimDataGridBundle\Extension\MassAction\MassActionDispatcher;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Akeneo\Platform\Bundle\UIBundle\Translator\TranslatorDecorator;
use Pim\Bundle\CustomEntityBundle\Action\Rest\CreateAction;
use Pim\Bundle\CustomEntityBundle\Action\Rest\DeleteAction;
use Pim\Bundle\CustomEntityBundle\Action\Rest\GetAction;
use Pim\Bundle\CustomEntityBundle\Action\Rest\GetAllAction;
use Pim\Bundle\CustomEntityBundle\Action\Rest\UpdateAction;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Entity\AbstractCustomEntity;
use Pim\Bundle\CustomEntityBundle\Event\ActionEventManager;
use Pim\Bundle\CustomEntityBundle\Manager\ManagerInterface;
use Pim\Bundle\CustomEntityBundle\Manager\Registry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RestActionsTest extends TestCase
{
    private function dependencies(): array
    {
        $factory = $this->createMock(ActionFactory::class);
        $events = $this->createMock(ActionEventManager::class);
        $registry = $this->createMock(Registry::class);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getEntityClass')->willReturn('ExampleEntity');
        $configuration->method('getName')->willReturn('example');
        $configuration->method('getActionOptions')->willReturn([]);
        $configuration->method('getOptions')->willReturn(['edit_form_extension' => []]);
        $manager = $this->createMock(ManagerInterface::class);
        $registry->method('getFromConfiguration')->with($configuration)->willReturn($manager);

        return [$factory, $events, $registry, $configuration, $manager];
    }

    public function testCreatePersistsAndReturnsEntityIdentity(): void
    {
        [$factory, $events, $registry, $configuration, $manager] = $this->dependencies();
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $entity = $this->createMock(AbstractCustomEntity::class);
        $entity->method('getId')->willReturn(17);
        $manager->expects(self::once())->method('create')->with('ExampleEntity', ['code' => 'x'])->willReturn($entity);
        $manager->expects(self::once())->method('save')->with($entity);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new \ArrayIterator());
        $normalizer = $this->createMock(NormalizerInterface::class);
        $action = new CreateAction($factory, $events, $registry, $validator, $normalizer);
        $action->setConfiguration($configuration);

        $response = $action->execute(new Request([], [], [], [], [], [], '{"code":"x"}'));
        self::assertSame(200, $response->getStatusCode());
        self::assertSame(['customEntityName' => 'example', 'id' => 17], json_decode($response->getContent(), true));
    }

    public function testCreateRejectsMalformedJson(): void
    {
        [$factory, $events, $registry, $configuration] = $this->dependencies();
        $validator = $this->createMock(ValidatorInterface::class);
        $action = new CreateAction($factory, $events, $registry, $validator, $this->createMock(NormalizerInterface::class));
        $action->setConfiguration($configuration);
        $this->expectException(BadRequestHttpException::class);
        $action->execute(new Request([], [], [], [], [], [], '{'));
    }

    public function testGetNormalizesEntityAndReturnsJson(): void
    {
        [$factory, $events, $registry, $configuration, $manager] = $this->dependencies();
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $entity = $this->createMock(AbstractCustomEntity::class);
        $manager->expects(self::once())->method('find')->with('ExampleEntity', '8', [])->willReturn($entity);
        $manager->expects(self::once())->method('normalize')->with($entity, 'internal_api', ['customEntityName' => 'example', 'form' => []])->willReturn(['id' => 8]);
        $action = new GetAction($factory, $events, $registry);
        $action->setConfiguration($configuration);

        $request = new Request();
        $request->attributes->set('id', '8');

        $response = $action->execute($request);
        self::assertSame(['id' => 8], json_decode($response->getContent(), true));
    }

    public function testGetAllNormalizesEachEntity(): void
    {
        [$factory, $events, $registry, $configuration, $manager] = $this->dependencies();
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $first = $this->createMock(AbstractCustomEntity::class);
        $second = $this->createMock(AbstractCustomEntity::class);
        $manager->expects(self::once())->method('findAll')->with('ExampleEntity')->willReturn([$first, $second]);
        $manager->expects(self::exactly(2))->method('normalize')->willReturnOnConsecutiveCalls(['id' => 1], ['id' => 2]);
        $action = new GetAllAction($factory, $events, $registry);
        $action->setConfiguration($configuration);
        self::assertSame([['id' => 1], ['id' => 2]], json_decode($action->execute(new Request())->getContent(), true));
    }

    public function testDeleteRemovesFoundEntityAndReturnsNoContent(): void
    {
        [$factory, $events, $registry, $configuration, $manager] = $this->dependencies();
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $entity = $this->createMock(AbstractCustomEntity::class);
        $manager->method('find')->willReturn($entity);
        $manager->expects(self::once())->method('remove')->with($entity);
        $action = new DeleteAction($factory, $events, $registry);
        $action->setConfiguration($configuration);

        $request = new Request();
        $request->attributes->set('id', '3');
        self::assertSame(204, $action->execute($request)->getStatusCode());
    }

    public function testUpdateUpdatesValidEntityAndReturnsNormalizedData(): void
    {
        [$factory, $events, $registry, $configuration, $manager] = $this->dependencies();
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $entity = $this->createMock(AbstractCustomEntity::class);
        $manager->method('find')->willReturn($entity);
        $manager->expects(self::once())->method('update')->with($entity, ['code' => 'new']);
        $manager->expects(self::once())->method('save')->with($entity);
        $manager->method('normalize')->willReturn(['code' => 'new']);
        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn(new \ArrayIterator());
        $action = new UpdateAction($factory, $events, $registry, $validator, $this->createMock(NormalizerInterface::class));
        $action->setConfiguration($configuration);

        $request = new Request([], [], ['id' => '4'], [], [], [], '{"code":"new"}');
        self::assertSame(['code' => 'new'], json_decode($action->execute($request)->getContent(), true));
    }

    public function testQuickExportLaunchesJobWithSelectionAndAuthenticatedUser(): void
    {
        $factory = $this->createMock(ActionFactory::class);
        $events = $this->createMock(ActionEventManager::class);
        $events->method('dispatchPostExecuteEvent')->willReturnArgument(1);
        $configuration = $this->createMock(ConfigurationInterface::class);
        $configuration->method('getActionOptions')->willReturn([]);
        $configuration->method('getEntityClass')->willReturn('ExampleEntity');
        $job = $this->createMock(JobInstance::class);
        $job->method('getRawParameters')->willReturn(['base' => true]);
        $jobs = $this->createMock(JobInstanceRepository::class);
        $jobs->method('findOneBy')->willReturn($job);
        $parser = $this->createMock(MassActionParametersParser::class);
        $parser->method('parse')->willReturn(['values' => [4, 9]]);
        $user = $this->createMock(UserInterface::class);
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->method('getToken')->willReturn($token);
        $launcher = $this->createMock(JobLauncherInterface::class);
        $launcher->expects(self::once())->method('launch')->with($job, $user, ['base' => true, 'reference_data' => 'ExampleEntity', 'ids' => [4, 9]]);
        $action = new QuickExportAction($factory, $events, $this->createMock(Registry::class), $this->createMock(RouterInterface::class), $this->createMock(TranslatorDecorator::class), $this->createMock(MassActionDispatcher::class), $jobs, $launcher, $storage, $parser, $this->createMock(GridFilterAdapterInterface::class));
        $action->setConfiguration($configuration);
        self::assertSame(200, $action->execute(new Request())->getStatusCode());
    }

    public function testRestActionsAdvertiseTheirTypes(): void
    {
        self::assertSame('rest_get', (new GetAction(...$this->dependenciesForRest()))->getType());
        self::assertSame('rest_getall', (new GetAllAction(...$this->dependenciesForRest()))->getType());
        self::assertSame('rest_delete', (new DeleteAction(...$this->dependenciesForRest()))->getType());
        [$factory, $events, $registry] = $this->dependenciesForRest();
        self::assertSame('rest_update', (new UpdateAction($factory, $events, $registry, $this->createMock(ValidatorInterface::class), $this->createMock(NormalizerInterface::class)))->getType());
    }

    private function dependenciesForRest(): array
    {
        return [$this->createMock(ActionFactory::class), $this->createMock(ActionEventManager::class), $this->createMock(Registry::class)];
    }
}
