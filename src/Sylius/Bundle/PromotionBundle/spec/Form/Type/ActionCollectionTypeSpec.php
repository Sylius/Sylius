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
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ActionCollectionTypeSpec extends ObjectBehavior
{
    public function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    public function it_is_initializabled()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\ActionCollectionType');
    }

    public function it_is_configuration_collection_type()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationCollectionType');
    }

    public function it_builds_prototypes(
        FormBuilderInterface $builder,
        FormBuilderInterface $prototype,
        FormInterface $form,
        $registry
    ) {
        $registry->all()->willReturn(array('configuration_kind' => ''));

        $builder->create('name', 'sylius_promotion_action', array('configuration_type' => 'configuration_kind'))
            ->willReturn($prototype);

        $prototype->getForm()->willReturn($form);

        $builder->setAttribute('prototypes', array('configuration_kind' => $form))->shouldBeCalled();

        $this->buildForm($builder, array(
            'registry' => $registry,
            'prototype_name' => 'name',
            'type' => 'sylius_promotion_action',
            'options' => array(),
        ));
    }

    public function it_builds_view(
        FormConfigInterface $config,
        FormView $view,
        FormInterface $form,
        FormInterface $prototype
    ) {
        $form->getConfig()->willReturn($config);
        $config->getAttribute('prototypes')->willReturn(array('configuration_kind' => $prototype));

        $prototype->createView($view)->shouldBeCalled();

        $this->buildView($view, $form, array());
    }

    public function it_should_have_default_option(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'type' => 'sylius_promotion_action',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))->shouldBeCalled()
        ;
        $this->setDefaultOptions($resolver);
    }

    public function it_has_collection_as_parent()
    {
        $this->getParent()->shouldReturn('collection');
    }

    public function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_action_collection');
    }
}
