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
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddPaymentMethodsFormSubscriber;
use Sylius\Component\Core\Model\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PaymentTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Payment::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentType');
    }
    
    function it_is_an_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form_with_proper_fields(FormBuilderInterface $builder)
    {
        $builder->addEventSubscriber(Argument::type(AddPaymentMethodsFormSubscriber::class))->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_configures_default_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('data_class', Payment::class)
            ->shouldBeCalled()
            ->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_checkout_payment');
    }
}
