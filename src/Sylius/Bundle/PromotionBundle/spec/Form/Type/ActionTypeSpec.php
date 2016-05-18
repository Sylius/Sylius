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
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildActionFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\ActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ActionTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $actionRegistry)
    {
        $this->beConstructedWith('Action', $actionRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ActionType::class);
    }

    function it_is_configuration_form_type()
    {
        $this->shouldHaveType(AbstractConfigurationType::class);
    }

    function it_builds_form(
        FormBuilder $builder,
        FormFactoryInterface $factory
    ) {
        $builder
            ->add('type', 'sylius_promotion_action_choice', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder->getFormFactory()->willReturn($factory);
        $builder->addEventSubscriber(
            Argument::type(BuildActionFormSubscriber::class)
        )->shouldBeCalled();

        $this->buildForm($builder, [
            'configuration_type' => 'configuration_form_type',
        ]);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_action');
    }
}
