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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PaymentGatewayChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array('offline' => 'Offline'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\PaymentGatewayChoiceType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array('offline' => 'Offline'),
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_a_name()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_a_parent()
    {
        $this->getName()->shouldReturn('sylius_payment_gateway_choice');
    }
}
