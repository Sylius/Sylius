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
use Prophecy\Argument;
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionActionFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class PromotionActionTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $actionRegistry)
    {
        $this->beConstructedWith('PromotionAction', $actionRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionActionType::class);
    }

    function it_is_a_configuration_form_type()
    {
        $this->shouldHaveType(AbstractConfigurationType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder, FormFactoryInterface $factory)
    {
        $builder->getFormFactory()->willReturn($factory);

        $builder
            ->add('type', 'sylius_promotion_action_choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder->addEventSubscriber(Argument::type(BuildPromotionActionFormSubscriber::class))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, [
            'configuration_type' => 'configuration_form_type',
        ]);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_action');
    }
}
