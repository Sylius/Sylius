<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Model\RuleInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleChoiceTypeSpec extends ObjectBehavior
{
    private $choices = [
        RuleInterface::TYPE_UNIT_TOTAL => 'Order total',
        RuleInterface::TYPE_UNIT_COUNT => 'Order units count',
    ];

    function let()
    {
        $this->beConstructedWith($this->choices);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\RuleChoiceType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_should_set_rule_types_to_choose_from(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => $this->choices])->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
