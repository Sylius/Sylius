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
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewAdminTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('dataClass', ['validation_group'], 'product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductReviewAdminType');
    }

    function it_extends_product_review_admin_type()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductReviewType');
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('rating', 'choice', [
                'choices' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5],
                'label' => 'sylius.form.review.rating',
                'expanded' => true,
                'multiple' => false,
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('author', 'sylius_customer_guest', [
                'label' => false,
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('title', 'text', [
                'label' => 'sylius.form.review.title',
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('comment', 'textarea', [
                'label' => 'sylius.form.review.comment',
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('author', 'entity', [
                'class' => 'Sylius\Component\Core\Model\Customer',
                'label' => 'sylius.form.review.author',
                'property' => 'email',
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('reviewSubject', 'entity', [
                'class' => 'Sylius\Component\Core\Model\Product',
                'label' => 'sylius.form.review.product',
                'property' => 'name',
            ])
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, ['rating_steps' => 5]);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_review_admin');
    }
}
