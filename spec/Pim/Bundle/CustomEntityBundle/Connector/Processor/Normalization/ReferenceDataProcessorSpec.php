<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Connector\Processor\Normalization\ReferenceDataProcessor;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ReferenceDataProcessorSpec extends ObjectBehavior
{
    public function let(
        NormalizerInterface $normalizer,
    ): void {
        $this->beConstructedWith($normalizer);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ReferenceDataProcessor::class);
    }

    public function it_processes_an_item(
        $normalizer,
        \stdClass $item,
    ): void {
        $normalizer->normalize($item, 'csv')->shouldBeCalled();
        $this->process($item);
    }
}
