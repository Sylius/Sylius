<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Variation\Checker\VariantsParityCheckerInterface;
use Sylius\Component\Variation\Generator\VariantGeneratorInterface;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;
use Sylius\Component\Variation\SetBuilder\SetBuilderInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class VariantGeneratorSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $variantFactory,
        SetBuilderInterface $setBuilder,
        VariantsParityCheckerInterface $variantsParityChecker
    ) {
        $this->beConstructedWith($variantFactory, $setBuilder, $variantsParityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement(VariantGeneratorInterface::class);
    }

    function it_cannot_generate_variants_for_an_object_without_options(VariableInterface $variable)
    {
        $variable->hasOptions()->willReturn(false);

        $this->shouldThrow(\InvalidArgumentException::class)->duringGenerate($variable);
    }

    function it_generates_variants_for_every_value_of_an_objects_single_option(
        FactoryInterface $variantFactory,
        OptionInterface $colorOption,
        OptionValueInterface $blackColor,
        OptionValueInterface $redColor,
        OptionValueInterface $whiteColor,
        SetBuilderInterface $setBuilder,
        VariableInterface $productVariable,
        VariantInterface $permutationVariant,
        VariantsParityCheckerInterface $variantsParityChecker
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $setBuilder->build([['black1', 'white2', 'red3']])->willReturn([['black1', 'white2', 'red3']]);

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(false);

        $variantFactory->createNew()->willReturn($permutationVariant);
        $permutationVariant->setObject($productVariable)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type(OptionValueInterface::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }

    function it_does_not_generate_variant_if_given_variant_exist(
        FactoryInterface $variantFactory,
        OptionInterface $colorOption,
        OptionValueInterface $blackColor,
        OptionValueInterface $redColor,
        OptionValueInterface $whiteColor,
        SetBuilderInterface $setBuilder,
        VariableInterface $productVariable,
        VariantInterface $permutationVariant,
        VariantsParityCheckerInterface $variantsParityChecker
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn([$colorOption]);

        $colorOption->getValues()->willReturn([$blackColor, $whiteColor, $redColor]);

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $setBuilder->build([['black1', 'white2', 'red3']])->willReturn([['black1', 'white2', 'red3']]);

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(true);

        $variantFactory->createNew()->willReturn($permutationVariant);
        $permutationVariant->setObject($productVariable)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type(OptionValueInterface::class))->shouldBeCalled();

        $productVariable->addVariant($permutationVariant)->shouldNotBeCalled();

        $this->generate($productVariable);
    }

    function it_generates_variants_for_every_possible_permutation_of_an_objects_options_and_option_values(
        FactoryInterface $variantFactory,
        OptionInterface $colorOption,
        OptionInterface $sizeOption,
        OptionValueInterface $blackColor,
        OptionValueInterface $largeSize,
        OptionValueInterface $mediumSize,
        OptionValueInterface $redColor,
        OptionValueInterface $smallSize,
        OptionValueInterface $whiteColor,
        SetBuilderInterface $setBuilder,
        VariableInterface $productVariable,
        VariantInterface $permutationVariant,
        VariantsParityCheckerInterface $variantsParityChecker
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

        $variantsParityChecker->checkParity($permutationVariant, $productVariable)->willReturn(false);

        $variantFactory->createNew()->willReturn($permutationVariant);
        $permutationVariant->setObject($productVariable)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type(OptionValueInterface::class))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }
}
