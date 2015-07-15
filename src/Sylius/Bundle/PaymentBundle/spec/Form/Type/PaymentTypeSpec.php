<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PaymentTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Payment', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\PaymentType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_builds_form(FormBuilderInterface $builder)
    {
        $builder->add('method', 'sylius_payment_method_choice', Argument::type('array'))->willReturn($builder);
        $builder->add('amount', 'sylius_money', Argument::type('array'))->willReturn($builder);
        $builder->add('state', 'choice', Argument::withKey('choices'))->willReturn($builder);

        $this->buildForm($builder, array());
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_payment');
    }
}
