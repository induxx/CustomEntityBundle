<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Reader\Database;

use Akeneo\Tool\Component\Batch\Job\JobParameters;
use Akeneo\Tool\Component\Batch\Model\StepExecution;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Pim\Bundle\CustomEntityBundle\Connector\Reader\Database\ReferenceDataReader;
use Akeneo\Tool\Component\Connector\Reader\Database\AbstractReader;

class ReferenceDataReaderSpec extends ObjectBehavior
{
    public function let(
        Registry $confRegistry,
        EntityManagerInterface $em,
        StepExecution $stepExecution,
    ): void {
        $this->beConstructedWith($confRegistry, $em);
        $this->setStepExecution($stepExecution);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ReferenceDataReader::class);
    }

    public function it_is_an_abstract_reader(): void
    {
        $this->shouldBeAnInstanceOf(AbstractReader::class);
    }

    public function it_reads_reference_data_from_database(
        $confRegistry,
        $em,
        $stepExecution,
        JobParameters $jobParameters,
        EntityRepository $repo,
        ConfigurationInterface $config,
    ): void {
        $bar = (fn ($id = null): object => new class ($id) {
            public function __construct(private $id)
            {
            }

            public function getId()
            {
                return $this->id;
            }
        });

        $jobParameters->get('reference_data_name')->willReturn('some_custom_entity');
        $stepExecution->getJobParameters()->willReturn($jobParameters);
        $config->getEntityClass()->willReturn('Foo\Bar');
        $confRegistry->get('some_custom_entity')->willReturn($config);

        $repo->findAll()->willReturn([$bar(42), $bar(56)]);
        $em->getRepository('Foo\Bar')->willReturn($repo);

        $stepExecution->incrementSummaryInfo('read')->shouldBeCalledTimes(2);

        $result = $this->read();
        $result->shouldHaveType($bar()::class);
        $result->getid()->shouldReturn(42);

        $result = $this->read();
        $result->shouldHaveType($bar()::class);
        $result->getid()->shouldReturn(56);

        $this->read()->shouldReturn(null);
    }
}
