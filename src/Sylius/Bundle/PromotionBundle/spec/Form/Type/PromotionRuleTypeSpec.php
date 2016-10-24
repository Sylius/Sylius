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
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildPromotionRuleFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class PromotionRuleTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $checkerRegistry)
    {
        $this->beConstructedWith('PromotionRule', $checkerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PromotionRuleType::class);
    }

    function it_is_configuration_form_type()
    {
        $this->shouldHaveType(AbstractConfigurationType::class);
    }

    function it_builds_a_form(FormBuilderInterface $builder, FormFactoryInterface $factory)
    {
        $builder->getFormFactory()->willReturn($factory);

        $builder
            ->add('type', 'sylius_promotion_rule_choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type(BuildPromotionRuleFormSubscriber::class))
            ->shouldBeCalled()
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, [
            'configuration_type' => 'configuration_form_type',
        ]);
    }

    function it_defines_an_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'PromotionRule',
                'validation_groups' => ['Default'],
            ])
            ->shouldBeCalled()
        ;

        $resolver->setDefined(['configuration_type'])->shouldBeCalled();
        $resolver->setDefaults(['configuration_type' => ItemTotalRuleChecker::TYPE])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion_rule');
    }
}
