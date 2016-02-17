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
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_returns_empty_string_if_array_is_empty()
    {
        $this->transform([])->shouldReturn('');
    }

    function it_throws_exception_if_not_array_transformed()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringTransform('foo')
        ;
    }

    function it_throws_exception_if_not_string_reverse_transformed()
    {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->duringTransform(false)
        ;
    }

    function it_transforms_array_into_string()
    {
        $this->transform(['foo', 'bar', 'yo'])->shouldReturn('foo, bar, yo');
    }

    function it_returns_empty_array_if_blanks_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn([]);
    }
}
