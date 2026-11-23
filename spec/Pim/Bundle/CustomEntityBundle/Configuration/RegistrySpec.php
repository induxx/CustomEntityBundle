<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Configuration;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Configuration\ConfigurationInterface;
use Pim\Bundle\CustomEntityBundle\Configuration\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RegistrySpec extends ObjectBehavior
{
    public function let(ContainerInterface $container): void
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Registry::class);
    }

    public function it_can_add_configurations(): void
    {
        $this->has('foo')->shouldReturn(false);
        $this->add('foo', 'bar');
        $this->has('foo')->shouldReturn(true);
    }

    public function it_can_get_names(): void
    {
        $this->getNames()->shouldReturn([]);

        $this->add('key1', 'service_1');
        $this->add('key2', 'service_2');
        $this->add('key3', 'service_3');

        $this->getNames()->shouldReturn(['key1', 'key2', 'key3']);
    }

    public function it_can_get_a_configuration(
        $container,
        ConfigurationInterface $config,
    ): void {
        $container->get('service_id')->willReturn($config);
        $this->add('foo', 'service_id');

        $this->get('foo')->shouldReturn($config);
    }
}
