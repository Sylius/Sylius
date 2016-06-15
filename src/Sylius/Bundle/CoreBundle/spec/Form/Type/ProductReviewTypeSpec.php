<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddAuthorGuestTypeFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\ProductReviewType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ReviewBundle\Form\Type\ReviewType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @mixin ProductReviewType
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductReviewTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ProductReview', ['sylius'], 'product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductReviewType');
    }

    function it_is_a_form_type()
    {
        $this->shouldHaveType(AbstractResourceType::class);
    }

    function it_is_a_review_form_type()
    {
        $this->shouldHaveType(ReviewType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_review');
    }

    function it_has_author_in_configuration(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'ProductReview',
            'validation_groups' => ['sylius'],
        ])->shouldBeCalled();

        $resolver->setDefaults([
            'rating_steps' => 5,
            'cascade_validation' => true,
        ])->shouldBeCalled();

        $resolver->setDefaults([
            'author' => null,
        ])->shouldBeCalled();

        $this->configureOptions($resolver);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('rating', 'choice', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('title', 'text', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('comment', 'textarea', Argument::type('array'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(new AddAuthorGuestTypeFormSubscriber())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, ['rating_steps' => 5]);
    }
}
