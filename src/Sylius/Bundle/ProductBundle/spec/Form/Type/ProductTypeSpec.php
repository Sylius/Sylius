<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductTypeSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('Product', array('sylius'));
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\Type\ProductType');
    }

    public function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    public function it_builds_form_with_proper_fields(FormBuilder $builder)
    {
        $builder
            ->add('masterVariant', 'sylius_product_variant', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('attributes', 'collection', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('options', 'sylius_product_option_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    public function it_defines_assigned_data_class(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => 'Product',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }

    public function it_has_valid_name()
    {
        $this->getName()->shouldReturn('sylius_product');
    }
}
