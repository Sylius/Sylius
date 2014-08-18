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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ArrayToStringTransformerSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(', ');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\DataTransformer\ArrayToStringTransformer');
    }

    public function it_implements_form_data_transformer_interface()
    {
        $this->shouldImplement('Symfony\Component\Form\DataTransformerInterface');
    }

    public function it_returns_empty_string_if_array_is_empty()
    {
        $this->transform(array())->shouldReturn('');
    }

    public function it_throws_exception_if_not_array_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform('foo')
        ;
    }

    public function it_throws_exception_if_not_string_reverse_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform(false)
        ;
    }

    public function it_transforms_array_into_string()
    {
        $this->transform(array('foo', 'bar', 'yo'))->shouldReturn('foo, bar, yo');
    }

    public function it_returns_empty_array_if_blanks_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(array());
    }
}
