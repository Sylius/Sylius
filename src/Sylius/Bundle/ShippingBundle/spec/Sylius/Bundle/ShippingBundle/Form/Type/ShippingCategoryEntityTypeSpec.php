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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingCategoryEntityTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ShippingCategory');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryEntityType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_entity_type_as_parent()
    {
        $this->getParent()->shouldReturn('entity');
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_class_name($resolver)
    {
        $resolver->setDefaults(array('class' => 'ShippingCategory'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
