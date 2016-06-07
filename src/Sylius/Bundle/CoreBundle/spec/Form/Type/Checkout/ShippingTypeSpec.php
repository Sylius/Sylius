<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShippingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin ShippingType
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ShippingTypeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Checkout\ShippingType');
    }

    function it_is_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('shipments', 'collection', [
                'type' => 'sylius_checkout_shipment',
                'options' => ['criteria' => ['enabled' => true]]
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_shop_checkout_shipping');
    }
}
