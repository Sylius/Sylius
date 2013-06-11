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

use PHPSpec2\ObjectBehavior;

/**
 * Promotion action form type spec.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ActionType extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\PromotionsBundle\Action\Registry\PromotionActionRegistryInterface $actionRegistry
     */
    function let($actionRegistry)
    {
        $this->beConstructedWith('Action', array('sylius'), $actionRegistry);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\Type\ActionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType('Symfony\Component\Form\AbstractType');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder          $builder
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function it_should_build_form_with_action_choice_field($builder, $factory)
    {
        $builder->addEventSubscriber(ANY_ARGUMENT)->willReturn($builder);
        $builder->getFormFactory()->willReturn($factory);

        $builder
            ->add('type', 'sylius_promotion_action_choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\Form\FormBuilder          $builder
     * @param Symfony\Component\Form\FormFactoryInterface $factory
     */
    function it_should_add_build_promotion_action_event_subscriber($builder, $factory)
    {
        $builder->add(ANY_ARGUMENTS)->willReturn($builder);
        $builder->getFormFactory()->willReturn($factory);

        $builder
            ->addEventSubscriber(\Mockery::type('Sylius\Bundle\PromotionsBundle\Form\EventListener\BuildActionFormListener'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'Action',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }
}
