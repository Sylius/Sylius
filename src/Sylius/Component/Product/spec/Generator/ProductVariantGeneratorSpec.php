<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Product\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Product\Generator\ProductVariantGenerator;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValue;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\SetBuilder\SetBuilderInterface;

/**
 * @mixin ProductVariantGenerator
 *
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
final class ProductVariantGeneratorSpec extends ObjectBehavior
{
    function let(FactoryInterface $variantFactory, SetBuilderInterface $setBuilder)
    {
        $this->beConstructedWith($variantFactory, $setBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantGenerator::class);
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement(ProductVariantGeneratorInterface::class);
    }

    function it_cannot_generate_variants_for_an_object_without_options(ProductInterface $variable)
    {
        $variable->hasOptions()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->duringGenerate($variable);
    }

    function it_generates_variants_for_every_value_of_an_objects_single_option(
        FactoryInterface $variantFactory,
        ProductOptionInterface $colorOption,
        ProductOptionValue $blackColor,
        ProductOptionValue $redColor,
        ProductOptionValue $whiteColor,
        SetBuilderInterface $setBuilder,
        ProductInterface $productVariable,
        ProductVariantInterface $permutationVariant
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);

        // Stubbing `ProductOptionValue` instead of `ProductOptionValueInterface` in order to stub `getId` method.
        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $setBuilder->build([
            ['black1', 'white2', 'red3'],
        ])->willReturn([
            ['black1', 'white2', 'red3'],
        ]);

        $variantFactory->createNew()->willReturn($permutationVariant);
        $permutationVariant->setProduct($productVariable)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type(ProductOptionValue::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }

    function it_generates_variants_for_every_possible_permutation_of_an_objects_options_and_option_values(
        FactoryInterface $variantFactory,
        ProductOptionInterface $colorOption,
        ProductOptionInterface $sizeOption,
        ProductOptionValue $blackColor,
        ProductOptionValue $largeSize,
        ProductOptionValue $mediumSize,
        ProductOptionValue $redColor,
        ProductOptionValue $smallSize,
        ProductOptionValue $whiteColor,
        SetBuilderInterface $setBuilder,
        ProductInterface $productVariable,
        ProductVariantInterface $permutationVariant
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption, $sizeOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);
        $sizeOption->getValues()->willReturn([$smallSize, $mediumSize, $largeSize]);

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');
        $smallSize->getId()->willReturn('small4');
        $mediumSize->getId()->willReturn('medium5');
        $largeSize->getId()->willReturn('large6');

        $setBuilder->build([
            ['black1', 'white2', 'red3'],
            ['small4', 'medium5', 'large6'],
        ])->willReturn([
            ['black1', 'small4'],
            ['black1', 'medium5'],
            ['black1', 'large6'],
            ['white2', 'small4'],
            ['white2', 'medium5'],
            ['white2', 'large6'],
            ['red3', 'small4'],
            ['red3', 'medium5'],
            ['red3', 'large6'],
        ]);

        $variantFactory->createNew()->willReturn($permutationVariant);
        $permutationVariant->setProduct($productVariable)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type(ProductOptionValue::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }
}
