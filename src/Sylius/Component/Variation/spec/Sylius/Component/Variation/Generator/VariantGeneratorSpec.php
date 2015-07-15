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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Variation\SetBuilder\SetBuilderInterface;
use Sylius\Component\Variation\Model\OptionInterface;
use Sylius\Component\Variation\Model\OptionValue;
use Sylius\Component\Variation\Model\VariableInterface;
use Sylius\Component\Variation\Model\VariantInterface;

/**
 * @author Adam Elsodaney <adam.elso@gmail.com>
 */
class VariantGeneratorSpec extends ObjectBehavior
{
    function let(RepositoryInterface $variantRepository, SetBuilderInterface $setBuilder)
    {
        $this->beConstructedWith($variantRepository, $setBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Variation\Generator\VariantGenerator');
    }

    function it_is_a_Sylius_variant_generator()
    {
        $this->shouldImplement('Sylius\Component\Variation\Generator\VariantGeneratorInterface');
    }

    function it_cannot_generate_variants_for_an_object_without_options(VariableInterface $variable)
    {
        $variable->hasOptions()->willReturn(false);

        $this->shouldThrow('InvalidArgumentException')->duringGenerate($variable);
    }

    function it_generates_variants_for_every_value_of_an_objects_single_option(
        VariableInterface $productVariable,
        RepositoryInterface $variantRepository,
        SetBuilderInterface $setBuilder,
        OptionInterface $colorOption,
        OptionValue $blackColor, OptionValue $whiteColor, OptionValue $redColor,
        VariantInterface $masterVariant, VariantInterface $permutationVariant
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn(array($colorOption));

        $colorOption->getValues()->willReturn(array($blackColor, $whiteColor, $redColor));

        // Stubbing `OptionValue` instead of `OptionValueInterface` in order to stub `getId` method.
        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');

        $setBuilder->build(array(
            array('black1', 'white2', 'red3'),
        ))->willReturn(array(
            array('black1', 'white2', 'red3'),
        ));

        $productVariable->getMasterVariant()->willReturn($masterVariant);

        $variantRepository->createNew()->willReturn($permutationVariant);
        $permutationVariant->setObject($productVariable)->shouldBeCalled();
        $permutationVariant->setDefaults($masterVariant)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type('Sylius\Component\Variation\Model\OptionValue'))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }

    function it_generates_variants_for_every_possible_permutation_of_an_objects_options_and_option_values(
        VariableInterface $productVariable,
        RepositoryInterface $variantRepository,
        SetBuilderInterface $setBuilder,
        OptionInterface $colorOption, OptionInterface $sizeOption,
        OptionValue $blackColor, OptionValue $whiteColor, OptionValue $redColor,
        OptionValue $smallSize, OptionValue $mediumSize, OptionValue $largeSize,
        VariantInterface $masterVariant, VariantInterface $permutationVariant
    ) {
        $productVariable->hasOptions()->willReturn(true);

        $productVariable->getOptions()->willReturn(array($colorOption, $sizeOption));

        $colorOption->getValues()->willReturn(array($blackColor, $whiteColor, $redColor));
        $sizeOption->getValues()->willReturn(array($smallSize, $mediumSize, $largeSize));

        $blackColor->getId()->willReturn('black1');
        $whiteColor->getId()->willReturn('white2');
        $redColor->getId()->willReturn('red3');
        $smallSize->getId()->willReturn('small4');
        $mediumSize->getId()->willReturn('medium5');
        $largeSize->getId()->willReturn('large6');

        $setBuilder->build(array(
            array('black1', 'white2', 'red3'),
            array('small4', 'medium5', 'large6')
        ))->willReturn(array(
            array('black1', 'small4'),
            array('black1', 'medium5'),
            array('black1', 'large6'),
            array('white2', 'small4'),
            array('white2', 'medium5'),
            array('white2', 'large6'),
            array('red3', 'small4'),
            array('red3', 'medium5'),
            array('red3', 'large6'),
        ));

        $productVariable->getMasterVariant()->willReturn($masterVariant);

        $variantRepository->createNew()->willReturn($permutationVariant);
        $permutationVariant->setObject($productVariable)->shouldBeCalled();
        $permutationVariant->setDefaults($masterVariant)->shouldBeCalled();
        $permutationVariant->addOption(Argument::type('Sylius\Component\Variation\Model\OptionValue'))->shouldBeCalled();
        $productVariable->addVariant($permutationVariant)->shouldBeCalled();

        $this->generate($productVariable);
    }
}
