<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type\Calculator;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolver; use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class FeeCalculatorChoiceTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(array());
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\Calculator\FeeCalculatorChoiceType');
    }

    public function it_is_abstract_type_object()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_sets_default_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array('choices' => array()))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_fee_calculator_choice');
    }
}
