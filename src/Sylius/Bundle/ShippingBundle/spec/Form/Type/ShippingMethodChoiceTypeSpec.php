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
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodChoiceType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gamil.com>
 */
final class ShippingMethodChoiceTypeSpec extends ObjectBehavior
{
    function let(
        ShippingMethodsResolverInterface $methodsResolver,
        ServiceRegistryInterface $calculators,
        RepositoryInterface $repository
    ) {
        $this->beConstructedWith($methodsResolver, $calculators, $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ShippingMethodChoiceType::class);
    }

    function it_is_a_form()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_adds_transformer_if_options_multiple_is_set(FormBuilderInterface $builder)
    {
        $builder->addModelTransformer(Argument::type(CollectionToArrayTransformer::class))->shouldBeCalled();

        $this->buildForm($builder, ['multiple' => true]);
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
