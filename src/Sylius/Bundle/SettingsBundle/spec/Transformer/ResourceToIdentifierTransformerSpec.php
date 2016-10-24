<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SettingsBundle\Transformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\spec\Fixture\ParameterFixture;
use Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface;
use Sylius\Bundle\SettingsBundle\Transformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourceToIdentifierTransformerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceToIdentifierTransformer::class);
    }

    function it_implements_parameter_transformer_interface()
    {
        $this->shouldImplement(ParameterTransformerInterface::class);
    }

    function it_returns_null_when_null_transformed()
    {
        $this->transform(null)->shouldReturn(null);
    }

    function it_transforms_object_into_its_identifier(ParameterFixture $object)
    {
        $object->getName()->willReturn('name');

        $this->transform($object)->shouldReturn('name');
    }

    function it_returns_null_when_null_reverse_transformed()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    function it_finds_object_when_identifier_reverse_transformed(
        ParameterFixture $object,
        RepositoryInterface $repository
    ) {
        $repository->findOneBy(['name' => 'foo'])->shouldBeCalled()->willReturn($object);

        $this->reverseTransform('foo')->shouldReturn($object);
    }

    function it_returns_null_when_object_was_not_found_on_reverse_transform($repository)
    {
        $repository->findOneBy(['name' => 'baz'])->shouldBeCalled()->willReturn(null);

        $this->reverseTransform('baz')->shouldReturn(null);
    }
}
