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
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ProductTypeSpec extends ObjectBehavior
{
    function let(CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith(Product::class, ['sylius'], $canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductType');
    }

    function it_extends_product_type_from_product_bundle()
    {
        $this->shouldHaveType(ProductType::class);
    }

    function it_builds_form(FormBuilderInterface $builder)
    {
        $builder
            ->add('masterVariant', 'sylius_product_variant', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('attributes', 'collection', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('associations', 'collection', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('options', 'sylius_product_option_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('translations', 'sylius_translations', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('shippingCategory', 'sylius_shipping_category_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('taxons', 'sylius_taxon_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('variantSelectionMethod', 'choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('channels', 'sylius_channel_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('restrictedZone', 'sylius_zone_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $builder
            ->add('mainTaxon', 'sylius_taxon_choice', Argument::cetera())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;
        $this->buildForm($builder, []);
    }
}
