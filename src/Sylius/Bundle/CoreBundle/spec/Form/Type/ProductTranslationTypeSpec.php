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
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductTranslationTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('ProductTranslation', [], 'sylius');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductTranslationType');
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'sylius.form.product.name',
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('description', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.product.description',
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('metaKeywords', 'text', [
                'required' => false,
                'label' => 'sylius.form.product.meta_keywords',
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('metaDescription', 'text', [
                'required' => false,
                'label' => 'sylius.form.product.meta_description',
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('shortDescription', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.product.short_description',
            ])
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, []);
    }

    function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_product_translation');
    }
}
