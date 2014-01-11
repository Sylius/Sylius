<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ArrayToStringTransformerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(', ');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\DataTransformer\ArrayToStringTransformer');
    }

    function it_implements_form_data_transformer_interface()
    {
        $this->shouldImplement('Symfony\Component\Form\DataTransformerInterface');
    }

    function it_returns_empty_string_if_array_is_empty()
    {
        $this->transform(array())->shouldReturn('');
    }

    function it_throws_exception_if_not_array_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform('foo')
        ;
    }

    function it_throws_exception_if_not_string_reverse_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform(false)
        ;
    }

    function it_transforms_array_into_string($coupon)
    {
        $this->transform(array('foo', 'bar', 'yo'))->shouldReturn('foo, bar, yo');
    }

    function it_returns_empty_array_if_blanks_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(array());
    }
}
