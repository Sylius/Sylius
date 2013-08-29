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
use Sylius\Bundle\ShippingBundle\Model\RuleInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class RuleChoiceTypeSpec extends ObjectBehavior
{
    private $choices = array(
        RuleInterface::TYPE_ITEM_TOTAL => 'Order total',
        RuleInterface::TYPE_ITEM_COUNT  => 'Order items count'
    );

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
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_set_rule_types_to_choose_from($resolver)
    {
        $resolver->setDefaults(array('choices' => $this->choices))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
