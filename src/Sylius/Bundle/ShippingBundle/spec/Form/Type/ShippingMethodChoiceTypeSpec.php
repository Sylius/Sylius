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
use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Component\Shipping\Resolver\MethodsResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
class ShippingMethodChoiceTypeSpec extends ObjectBehavior
{
    function let(
        MethodsResolverInterface $resolver,
        CalculatorRegistryInterface $calculators,
        RepositoryInterface $repository
    ) {
        $this->beConstructedWith($resolver, $calculators, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodChoiceType');
    }

    function it_is_a_form()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_builds_a_form(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(
            Argument::type('Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer')
        )->shouldBeCalled();

        $this->buildForm($builder, array(
            'multiple' => true
        ));
    }

    function it_has_options(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(Argument::withKey('choice_list'))->shouldBeCalled()->willReturn($resolver);
        $resolver->setOptional(array(
            'subject',
        ))->shouldBeCalled()->willReturn($resolver);
        $resolver->setAllowedTypes(array(
            'subject'  => array('Sylius\Component\Shipping\Model\ShippingSubjectInterface'),
            'criteria' => array('array')
        ))->shouldBeCalled()->willReturn($resolver);

        $this->setDefaultOptions($resolver);
    }

    function it_has_a_parent()
    {
        $this->getParent()->shouldReturn('choice');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_shipping_method_choice');
    }
}
