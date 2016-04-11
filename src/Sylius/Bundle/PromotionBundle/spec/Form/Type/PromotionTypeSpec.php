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
use Sylius\Bundle\PromotionBundle\Form\Type\ActionCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\RuleCollectionType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class PromotionTypeSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $checkerRegistry,
        ServiceRegistryInterface $actionRegistry
    ) {
        $this->beConstructedWith('Promotion', ['sylius'], $checkerRegistry, $actionRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionBundle\Form\Type\PromotionType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldHaveType(AbstractType::class);
    }

    function it_should_build_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', TextType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('description', TextType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('exclusive', CheckboxType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('usageLimit', IntegerType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('startsAt', DateTimeType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('endsAt', DateTimeType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('couponBased', CheckboxType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('rules', RuleCollectionType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('actions', ActionCollectionType::class, Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_should_define_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'Promotion',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_promotion');
    }
}
