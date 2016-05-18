<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\RuleCollectionType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RuleCollectionTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RuleCollectionType::class);
    }

    function it_is_configuration_collection_type()
    {
        $this->shouldHaveType(AbstractConfigurationCollectionType::class);
    }

    function it_builds_prototypes(
        FormBuilderInterface $builder,
        FormBuilderInterface $prototype,
        FormInterface $form,
        $registry
    ) {
        $registry->all()->willReturn(['configuration_kind' => '']);

        $builder->create('name', 'sylius_promotion_rule', ['configuration_type' => 'configuration_kind'])
            ->willReturn($prototype);

        $prototype->getForm()->willReturn($form);

        $builder->setAttribute('prototypes', ['configuration_kind' => $form])->shouldBeCalled();

        $this->buildForm($builder, [
            'registry' => $registry,
            'prototype_name' => 'name',
            'type' => 'sylius_promotion_rule',
            'options' => [],
        ]);
    }

    function it_builds_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $prototype
    ) {
        $form->getConfig()->willReturn($config);
        $config->getAttribute('prototypes')->willReturn(['configuration_kind' => $prototype]);

        $prototype->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, []);
    }

    function it_should_have_default_option(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'type' => 'sylius_promotion_rule',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])->shouldBeCalled()
        ;
        $this->configureOptions($resolver);
    }

    function it_should_have_parent()
    {
        $this->getParent()->shouldReturn('collection');
    }

    function it_should_have_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_rule_collection');
    }
}
