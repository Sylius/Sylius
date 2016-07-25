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
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class PaymentStepTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['sylius', 'checkout']);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\Checkout\PaymentStepType');
    }

    function it_is_an_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_builds_form_with_proper_fields(FormBuilderInterface $builder)
    {
        $builder
            ->add('payments', 'collection', [
                'type' => 'sylius_checkout_payment',
                'label' => false,
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_configures_default_validation_groups(OptionsResolver $resolver)
    {
        $resolver
            ->setDefault('validation_groups', ['sylius', 'checkout'])
            ->shouldBeCalled()
            ->willReturn($resolver);

        $this->configureOptions($resolver);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_checkout_payment_step');
    }
}
