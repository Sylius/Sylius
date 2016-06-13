<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\MethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class PaymentMethodChoiceTypeSpec extends ObjectBehavior
{
    function let(MethodsResolverInterface $compositeMethodsResolver, RepositoryInterface $paymentMethodRepository)
    {
        $this->beConstructedWith($compositeMethodsResolver, $paymentMethodRepository);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentBundle\Form\Type\PaymentMethodChoiceType');
    }
    
    function it_is_an_abstract_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_adds_transformer_if_options_multiple_is_set(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(Argument::type(CollectionToArrayTransformer::class))->shouldBeCalled();

        $this->buildForm($builder, ['multiple' => true]);
    }
    
    public function it_configures_options(OptionsResolver $resolver) 
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setDefined(Argument::type('array'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setAllowedTypes('subject', PaymentInterface::class)->shouldBeCalled()->willReturn($resolver);
        
        $this->configureOptions($resolver);
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_payment_method_choice');
    }
}
