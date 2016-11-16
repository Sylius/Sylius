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
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGenerator;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
final class ProductVariantGeneratorSpec extends ObjectBehavior
{
    function let(
        ProductVariantFactoryInterface $productVariantFactory,
        ProductVariantsParityCheckerInterface $variantsParityChecker
    ) {
        $this->beConstructedWith($productVariantFactory, $variantsParityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantGenerator::class);
    }

    function it_implements_product_variant_generator_interfave()
    {
        $this->shouldImplement(ProductVariantGeneratorInterface::class);
    }

    function it_cannot_generate_variants_for_an_object_without_options(ProductInterface $variable)
    {
        $variable->hasOptions()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->duringGenerate($variable);
    }

    function it_generates_variants_for_every_value_of_an_objects_single_option(
        ProductInterface $productVariable,
        ProductOptionInterface $colorOption,
        ProductOptionValueInterface $blackColor,
        ProductOptionValueInterface $redColor,
        ProductOptionValueInterface $whiteColor,
        ProductVariantFactoryInterface $productVariantFactory,
        ProductVariantInterface $permutationVariant,
        ProductVariantsParityCheckerInterface $variantsParityChecker
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(false);

        $productVariantFactory->createForProduct($productVariable)->willReturn($permutationVariant);

        $permutationVariant->addOptionValue(Argument::type(ProductOptionValueInterface::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }

    function it_does_not_generate_variant_if_given_variant_exists(
        ProductInterface $productVariable,
        ProductOptionInterface $colorOption,
        ProductOptionValueInterface $blackColor,
        ProductOptionValueInterface $redColor,
        ProductOptionValueInterface $whiteColor,
        ProductVariantFactoryInterface $productVariantFactory,
        ProductVariantInterface $permutationVariant,
        ProductVariantsParityCheckerInterface $variantsParityChecker
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(true);

        $productVariantFactory->createForProduct($productVariable)->willReturn($permutationVariant);

        $permutationVariant->addOptionValue(Argument::type(ProductOptionValueInterface::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldNotBeCalled();

        $this->generate($productVariable);
    }

    function it_generates_variants_for_every_possible_permutation_of_an_objects_options_and_option_values(
        ProductInterface $productVariable,
        ProductOptionInterface $colorOption,
        ProductOptionInterface $sizeOption,
        ProductOptionValueInterface $blackColor,
        ProductOptionValueInterface $largeSize,
        ProductOptionValueInterface $mediumSize,
        ProductOptionValueInterface $redColor,
        ProductOptionValueInterface $smallSize,
        ProductOptionValueInterface $whiteColor,
        ProductVariantFactoryInterface $productVariantFactory,
        ProductVariantInterface $permutationVariant,
        ProductVariantsParityCheckerInterface $variantsParityChecker
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

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(false);

        $productVariantFactory->createForProduct($productVariable)->willReturn($permutationVariant);

        $permutationVariant->addOptionValue(Argument::type(ProductOptionValueInterface::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }
}
