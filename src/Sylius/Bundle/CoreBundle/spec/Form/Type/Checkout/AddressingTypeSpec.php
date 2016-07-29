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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddDefaultBillingAddressOnOrderFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressingType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddCustomerGuestTypeFormSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin AddressingType
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class AddressingTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('Order', []);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Checkout\AddressingType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->add('shippingAddress', 'sylius_address', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->add('billingAddress', 'sylius_address')->shouldBeCalled()->willReturn($builder);
        $builder->add('differentBillingAddress', 'checkbox', Argument::type('array'))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(new AddCustomerGuestTypeFormSubscriber('customer'))->shouldBeCalled()->willReturn($builder);
        $builder->addEventSubscriber(new AddDefaultBillingAddressOnOrderFormSubscriber())->shouldBeCalled()->willReturn($builder);

        $this->buildForm($builder, []);
    }

    function it_has_default_configuration_and_cascade_validation_available(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Order', 'validation_groups' => []])->shouldBeCalled();
        $resolver->setDefaults(['customer' => null, 'cascade_validation' => true])->shouldBeCalled();

        $this->configureOptions($resolver);
    }
}
