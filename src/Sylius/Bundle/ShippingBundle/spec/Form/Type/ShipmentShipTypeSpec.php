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
use Prophecy\Argument;
use Sylius\Bundle\ShippingBundle\Form\Type\ShipmentShipType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @mixin ShipmentShipType
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ShipmentShipTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ShipmentShip', ['sylius']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\ShipmentShipType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('tracking', 'text', Argument::type('array'))->shouldBeCalled()->willreturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_shipment_ship');
    }
}
