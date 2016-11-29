<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\RecursiveTransformer;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class RecursiveTransformerSpec extends ObjectBehavior
{
    function let(DataTransformerInterface $transformer)
    {
        $this->beConstructedWith($transformer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RecursiveTransformer::class);
    }

    function it_is_data_transformer()
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_transforms_recursively_using_configured_transformer(
        DataTransformerInterface $transformer,
        ResourceInterface $firstTaxon,
        ResourceInterface $secondTaxon,
        ResourceInterface $thirdTaxon
    ) {
        $transformer->transform($firstTaxon)->willReturn('abc');
        $transformer->transform($secondTaxon)->willReturn('cde');
        $transformer->transform($thirdTaxon)->willReturn('fgh');

        $this->transform([$firstTaxon, $secondTaxon, $thirdTaxon])->shouldReturn(['abc', 'cde', 'fgh']);
    }

    function it_reverse_transforms_using_configured_transformer(
        DataTransformerInterface $transformer,
        ResourceInterface $firstTaxon,
        ResourceInterface $secondTaxon,
        ResourceInterface $thirdTaxon
    ) {
        $transformer->reverseTransform('abc')->willReturn($firstTaxon);
        $transformer->reverseTransform('cde')->willReturn($secondTaxon);
        $transformer->reverseTransform('fgh')->willReturn($thirdTaxon);

        $this->reverseTransform(['abc', 'cde', 'fgh'])->shouldReturn([$firstTaxon, $secondTaxon, $thirdTaxon]);
    }

    function it_throws_invalid_argument_exception_if_transform_argument_is_not_array()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('transform', [new \stdClass()]);
        $this->shouldThrow(\InvalidArgumentException::class)->during('reverseTransform', [new \stdClass()]);
    }
}
