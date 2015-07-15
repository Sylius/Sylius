<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VolumeBasedConfigurationTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\Type\VolumeBasedConfigurationType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'    => null,
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false,
            'label'         => 'sylius.form.pricing.volume_based.label',
            'type'          => 'sylius_price_calculator_volume_based_configuration'
        ))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('collection');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_price_calculator_volume_based');
    }
}
