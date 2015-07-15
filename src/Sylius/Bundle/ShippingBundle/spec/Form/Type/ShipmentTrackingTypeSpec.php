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
use Symfony\Component\Form\Test\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class ShipmentTrackingTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('ShipmentTracking', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\ShipmentTrackingType');
    }

    public function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    public function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('tracking', 'text', Argument::type('array'))->shouldBeCalled()->willreturn($builder);

        $this->buildForm($builder, array(
            'multiple' => true,
        ));
    }

    public function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_shipment_tracking');
    }
}
