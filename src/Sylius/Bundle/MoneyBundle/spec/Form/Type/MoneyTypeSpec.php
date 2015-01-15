<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('PLN');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Form\Type\MoneyType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_money_type_as_parent()
    {
        $this->getParent()->shouldReturn('money');
    }

    function it_defines_assigned_currency_and_sets_divisor_to_100(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'currency' => 'PLN',
                'divisor' => 100)
            )
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
