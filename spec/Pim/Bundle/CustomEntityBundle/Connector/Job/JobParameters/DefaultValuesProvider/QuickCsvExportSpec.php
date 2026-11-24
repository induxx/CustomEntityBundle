<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider;

use Akeneo\Tool\Component\Batch\Job\JobInterface;
use Akeneo\Tool\Component\Batch\Job\JobParameters\DefaultValuesProviderInterface;
use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\Job\JobParameters\DefaultValuesProvider\QuickCsvExport;

class QuickCsvExportSpec extends ObjectBehavior
{
    public function let(DefaultValuesProviderInterface $simpleProvider): void
    {
        $this->beConstructedWith($simpleProvider, ['a_job_name']);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(QuickCsvExport::class);
    }

    public function it_is_a_default_values_provider(): void
    {
        $this->shouldImplement(DefaultValuesProviderInterface::class);
    }

    public function it_only_provides_values_for_supported_jobs(
        JobInterface $supportedJob,
        JobInterface $unsupportedJob,
    ): void {
        $supportedJob->getName()->willReturn('a_job_name');
        $unsupportedJob->getName()->willReturn('another_job_name');

        $this->supports($supportedJob)->shouldReturn(true);
        $this->supports($unsupportedJob)->shouldReturn(false);
    }

    public function it_provides_default_values(
        $simpleProvider,
    ): void {
        $simpleProvider->getDefaultValues()->willReturn([]);
        $this->getDefaultValues()->shouldReturn(
            [
                'reference_data' => null,
                'ids'            => null,
            ]
        );
    }
}
