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
use Sylius\Bundle\CoreBundle\Form\Type\ProductReviewType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
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
        $this->shouldHaveType(ProductReviewType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('rating', 'choice', Argument::cetera())
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('author', 'sylius_customer_guest', Argument::cetera())
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('title', 'text', Argument::cetera())
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('comment', 'textarea', Argument::cetera())
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('author', 'entity', Argument::cetera())
            ->willReturn($builder)
            ->shouldBeCalled()
        ;

        $builder
            ->add('reviewSubject', 'entity', Argument::cetera())
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
