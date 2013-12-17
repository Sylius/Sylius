<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PromotionsBundle\Checker\Registry\RuleCheckerRegistry;
use Sylius\Bundle\PromotionsBundle\Checker\RuleCheckerInterface;
use Sylius\Bundle\PromotionsBundle\Model\RuleInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RuleCollectionTypeSpec extends ObjectBehavior
{

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\Type\RuleCollectionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    function it_should_build_prototype(
        FormBuilderInterface $builder,
        RuleCheckerRegistry $registry,
        FormBuilderInterface $prototype,
        FormInterface $form)
    {
        $registry->getCheckers()
            ->shouldBeCalled()
            ->willReturn(array(
                RuleInterface::TYPE_ITEM_TOTAL => $prototype
            ));

        $builder->create(Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($prototype);

        $prototype->getForm()
            ->shouldBeCalled()
            ->willReturn($form);

        $builder->setAttribute('prototypes', array(
            RuleInterface::TYPE_ITEM_TOTAL => $form
        ))
            ->shouldBeCalled();

        $this->buildForm($builder, array(
            'registry' => $registry,
            'prototype_name' => 'name',
            'type' => 'sylius_rule_collection',
            'options' => array(),
        ));
    }

    function it_should_build_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $prototype)
    {
        $config->getAttribute('prototypes')
            ->shouldBeCalled()
            ->willReturn(array(
                RuleInterface::TYPE_ITEM_TOTAL => $prototype
            ));

        $prototype->createView($view)
            ->shouldBeCalled();

        $form->getConfig()
            ->shouldBeCalled()
            ->willReturn($config);

        $this->buildView($view, $form, array());
    }

    function it_should_have_default_option(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setOptional(array(
                'registry',
                'rule_type'
            ))->shouldBeCalled();

        $resolver
            ->setDefaults(array(
                'type'             => 'sylius_promotion_rule',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
                'label'            => 'sylius.form.promotion.rules',
                'button_add_label' => 'sylius.promotion.add_rule',
                'rule_type'        => RuleInterface::TYPE_ITEM_TOTAL,
            ))
            ->shouldBeCalled();
        ;

        $this->setDefaultOptions($resolver);
    }

    function it_should_have_parent()
    {
        $this->getParent()->shouldReturn('collection');
    }

    function it_should_have_name()
    {
        $this->getName()->shouldReturn('sylius_rule_collection');
    }
}
