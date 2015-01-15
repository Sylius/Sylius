<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormBuilder;

/**
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class AddressTypeSpec extends ObjectBehavior
{
    function let(EventSubscriberInterface $eventListener)
    {
        $this->beConstructedWith('Address', array('sylius'), $eventListener);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Form\Type\AddressType');
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_has_a_valid_name()
    {
        $this->getName()->shouldReturn('sylius_address');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder->addEventSubscriber(Argument::type('Symfony\Component\EventDispatcher\EventSubscriberInterface'))
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('firstName', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('lastName', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('phoneNumber', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('company', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('country', 'sylius_country_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('street', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('city', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $builder
            ->add('postcode', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder);

        $this->buildForm($builder, array());
    }
}
