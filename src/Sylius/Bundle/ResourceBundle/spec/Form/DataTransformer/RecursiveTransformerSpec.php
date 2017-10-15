<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class RecursiveTransformerSpec extends ObjectBehavior
{
    function let(DataTransformerInterface $decoratedTransformer): void
    {
        $this->beConstructedWith($decoratedTransformer);
    }

    function it_is_data_transformer(): void
    {
        $this->shouldImplement(DataTransformerInterface::class);
    }

    function it_returns_an_empty_array_collection_when_transforming_a_null(
        DataTransformerInterface $decoratedTransformer
    ): void {
        $this->transform(null)->shouldBeLike(new ArrayCollection());
        $decoratedTransformer->transform(Argument::any())->shouldNotBeCalled();
    }

    function it_returns_an_empty_array_collection_when_reverse_transforming_a_null(
        DataTransformerInterface $decoratedTransformer
    ): void {
        $this->reverseTransform(null)->shouldBeLike(new ArrayCollection());
        $decoratedTransformer->reverseTransform(Argument::any())->shouldNotBeCalled();
    }

    function it_transforms_recursively_using_configured_transformer(DataTransformerInterface $decoratedTransformer): void
    {
        $decoratedTransformer->transform('ABC')->willReturn('abc');
        $decoratedTransformer->transform('CDE')->willReturn('cde');
        $decoratedTransformer->transform('FGH')->willReturn('fgh');

        $this->transform(new ArrayCollection(['ABC', 'CDE', 'FGH']))->shouldBeLike(new ArrayCollection(['abc', 'cde', 'fgh']));
    }

    function it_reverse_transforms_using_configured_transformer(DataTransformerInterface $decoratedTransformer): void
    {
        $decoratedTransformer->reverseTransform('abc')->willReturn('ABC');
        $decoratedTransformer->reverseTransform('cde')->willReturn('CDE');
        $decoratedTransformer->reverseTransform('fgh')->willReturn('FGH');

        $this->reverseTransform(new ArrayCollection(['abc', 'cde', 'fgh']))->shouldBeLike(new ArrayCollection(['ABC', 'CDE', 'FGH']));
    }

    function it_throws_transformation_failed_exception_if_transform_argument_is_not_collection_or_null(): void
    {
        $this->shouldThrow(TransformationFailedException::class)->during('transform', [new \stdClass()]);
        $this->shouldThrow(TransformationFailedException::class)->during('reverseTransform', [new \stdClass()]);
    }
}
