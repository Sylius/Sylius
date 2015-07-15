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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\SettingsBundle\Model\ParameterInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ObjectToIdentifierTransformerSpec extends ObjectBehavior
{
    public function let(ObjectRepository $repository)
    {
        $this->beConstructedWith($repository, 'name');
    }

    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SettingsBundle\Transformer\ObjectToIdentifierTransformer');
    }

    public function it_should_implement_parameter_transformer_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SettingsBundle\Transformer\ParameterTransformerInterface');
    }

    public function it_should_return_null_when_null_transformed()
    {
        $this->transform(null)->shouldReturn(null);
    }

    public function it_should_transform_object_into_its_identifier(ParameterInterface $object)
    {
        $object->getName()->willReturn('name');

        $this->transform($object)->shouldReturn('name');
    }

    public function it_should_return_null_when_null_reverse_transformed()
    {
        $this->reverseTransform(null)->shouldReturn(null);
    }

    public function it_should_find_object_when_identifier_reverse_transformed(
        $repository,
        ParameterInterface $object
    ) {
        $repository->findOneBy(array('name' => 'foo'))->shouldBeCalled()->willReturn($object);

        $this->reverseTransform('foo')->shouldReturn($object);
    }

    public function it_should_null_when_object_not_found_on_reverse_transform($repository)
    {
        $repository->findOneBy(array('name' => 'baz'))->shouldBeCalled()->willReturn(null);

        $this->reverseTransform('baz')->shouldReturn(null);
    }
}
