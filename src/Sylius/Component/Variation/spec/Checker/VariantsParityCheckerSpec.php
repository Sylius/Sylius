<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Variation\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Variation\Checker\VariantsParityCheckerInterface;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValueInterface;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @mixin VariantsParityCheckerInterface
 * 
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class VariantsParityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Checker\VariantsParityChecker');
    }

    function it_implements_variants_parity_checker_interface()
    {
        $this->shouldImplement(VariantsParityCheckerInterface::class);
    }

    function it_matches_if_given_variable_contains_similar_variant(
        OptionInterface $color,
        OptionInterface $size,
        OptionValueInterface $blueColor,
        OptionValueInterface $mediumSize,
        VariableInterface $tShirt,
        VariantInterface $checkMediumBlueTShirt,
        VariantInterface $mediumBlueTShirt,
        VariantInterface $smallBlueTShirt
    ) {
        $tShirt->getVariants()->willReturn([$smallBlueTShirt, $mediumBlueTShirt]);
        $tShirt->getOptions()->willReturn([$color, $size]);

        $smallBlueTShirt->hasOption($blueColor)->willReturn(true);
        $smallBlueTShirt->hasOption($mediumSize)->willReturn(false);
        $mediumBlueTShirt->hasOption($blueColor)->willReturn(true);
        $mediumBlueTShirt->hasOption($mediumSize)->willReturn(true);

        $checkMediumBlueTShirt->getOptions()->willReturn([$blueColor, $mediumSize]);

        $this->checkParity($checkMediumBlueTShirt, $tShirt)->shouldReturn(true);
    }

    function it_returns_false_if_variable_does_not_contain_variant_with_same_options_set(
        OptionInterface $color,
        OptionInterface $size,
        OptionValueInterface $blueColor,
        OptionValueInterface $largeSize,
        VariableInterface $tShirt,
        VariantInterface $checkLargeBlueTShirt,
        VariantInterface $mediumBlueTShirt,
        VariantInterface $smallBlueTShirt
    ) {
        $tShirt->getVariants()->willReturn([$smallBlueTShirt, $mediumBlueTShirt]);
        $tShirt->getOptions()->willReturn([$color, $size]);

        $smallBlueTShirt->hasOption($blueColor)->willReturn(true);
        $smallBlueTShirt->hasOption($largeSize)->willReturn(false);
        $mediumBlueTShirt->hasOption($blueColor)->willReturn(true);
        $mediumBlueTShirt->hasOption($largeSize)->willReturn(false);

        $checkLargeBlueTShirt->getOptions()->willReturn([$blueColor, $largeSize]);

        $this->checkParity($checkLargeBlueTShirt, $tShirt)->shouldReturn(false);
    }

    function it_matches_if_given_variable_has_given_variant(
        OptionInterface $color,
        OptionInterface $size,
        OptionValueInterface $blueColor,
        OptionValueInterface $mediumSize,
        VariableInterface $tShirt,
        VariantInterface $checkMediumBlueTShirt
    ) {
        $tShirt->getVariants()->willReturn([$checkMediumBlueTShirt]);
        $tShirt->getOptions()->willReturn([$color, $size]);

        $checkMediumBlueTShirt->getOptions()->willReturn([$blueColor, $mediumSize]);

        $this->checkParity($checkMediumBlueTShirt, $tShirt)->shouldReturn(false);
    }


    function it_throws_an_exception_if_number_of_possible_options_on_variable_and_number_of_options_values_on_variant_are_equal(
        OptionInterface $color,
        OptionInterface $size,
        OptionValueInterface $blueColor,
        VariableInterface $tShirt,
        VariantInterface $checkMediumBlueTShirt
    ) {
        $tShirt->getOptions()->willReturn([$color, $size]);

        $checkMediumBlueTShirt->getOptions()->willReturn([$blueColor]);

        $this
            ->shouldThrow(
                new \InvalidArgumentException(
                    'Number of set option values should be equal to number of available options.'
                )
            )->during('checkParity', [$checkMediumBlueTShirt, $tShirt])
        ;
    }
}
