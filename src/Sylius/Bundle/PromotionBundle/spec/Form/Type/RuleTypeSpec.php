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
use Sylius\Bundle\PromotionBundle\Form\EventListener\BuildRuleFormSubscriber;
use Sylius\Bundle\PromotionBundle\Form\Type\Core\AbstractConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\RuleType;
use Sylius\Component\Promotion\Checker\ItemTotalRuleChecker;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class RuleTypeSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $checkerRegistry)
    {
        $this->beConstructedWith('Rule', $checkerRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RuleType::class);
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
            ->add('type', 'sylius_promotion_rule_choice', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder->getFormFactory()->willReturn($factory);
        $builder->addEventSubscriber(
            Argument::type(BuildRuleFormSubscriber::class)
        )->shouldBeCalled();

        $this->buildForm($builder, [
            'configuration_type' => 'configuration_form_type',
        ]);
    }

    function it_should_define_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Rule',
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
