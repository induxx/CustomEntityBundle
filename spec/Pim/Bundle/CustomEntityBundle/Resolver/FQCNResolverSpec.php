<?php

namespace spec\Pim\Bundle\CustomEntityBundle\Resolver;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CustomEntityBundle\Resolver\FQCNResolver;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class FQCNResolverSpec extends ObjectBehavior
{
    public function let(ContainerInterface $container): void
    {
        $container->getParameter(
            Argument::containingString('pim_catalog.entity.')
        )->willThrow(
            InvalidArgumentException::class
        );
        $this->beConstructedWith($container);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FQCNResolver::class);
    }

    public function it_resolves_fqcn_for_custom_entities($container): void
    {
        $container->getParameter('pim_custom_entity.entity.baz.class')->willReturn('Foo\Bar\Baz');
        $this->getFQCN('baz')->shouldReturn('Foo\Bar\Baz');
    }

    public function it_returns_null_for_an_unknown_parameter($container): void
    {
        $container->getParameter('pim_custom_entity.entity.baz.class')->willThrow(InvalidArgumentException::class);
        $this->getFQCN('baz')->shouldReturn(null);
    }
}
